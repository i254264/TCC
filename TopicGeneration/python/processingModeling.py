import sys
import os
import nltk
from nltk.corpus import stopwords
import pandas as pd
import re
#Gensim
import gensim
from gensim import corpora
from gensim.models import Word2Vec
from nltk.corpus import wordnet
from nltk.stem import WordNetLemmatizer
from sklearn.cluster import KMeans
import numpy as np
from sklearn.manifold import TSNE
import json
import mysql.connector
from mysql.connector import Error

stop_words = stopwords.words('english')
clean = sys.argv[1]
clean = re.sub("[^0-9]", "", clean)
lemma = sys.argv[2]
lemma = re.sub("[^0-9]", "", lemma)
topics = sys.argv[3]
topics = re.sub("[^0-9]", "", topics)
words = sys.argv[4]
words = re.sub("[^0-9]", "", words)
interaction = sys.argv[5]
interaction = re.sub("[^0-9]", "", interaction)
typeModeling = sys.argv[6]
typeModeling = re.sub("[^0-9]", "", typeModeling)

def Document_Cleansing(Document):
    # Verificar se Document é um valor nulo
    if not Document or pd.isna(Document):
        return ''  # Retorna uma string vazia para valores nulos

    Document = Document.lower() # Lowercase primeiro
    Document = re.sub(r'(\\n)', ' ', Document) # Remove quebras de linha
    # Remove e-mails ou URLs se necessário (opcional)
    # Remove números complexos/pontuação, mas mantém espaço para o tokenizador
    Document = re.sub(r'(\d+),(\d+),?(\d*)', " ", Document)
    # Remove caracteres especiais exceto letras
    Document = re.sub(r'[^a-zA-Z\s]', ' ', Document)

    return Document

try:
    connection = mysql.connector.connect(host='localhost',
                                         database='topicgeneration',
                                         user='root',
                                         password='')
    connection.autocommit = True
except Error as e:
    print("Erro SQL")
    exit()

array_df = []

if connection.is_connected():
    cursor = connection.cursor()
    
    # Identifica dinamicamente a primeira coluna que não seja 'id'
    cursor.execute("SHOW COLUMNS FROM tabela_topicgeneration")
    columns = [row[0] for row in cursor.fetchall() if row[0].lower() != 'id']
    
    if not columns:
        print("Erro: Nenhuma coluna de dados encontrada.")
        sys.exit(1)
    
    target_col = columns[0] 
    
    # EXECUTAR A QUERY SEM FETCHALL (Streaming)
    cursor.execute(f"SELECT `{target_col}` FROM tabela_topicgeneration")
    
    # Iterar sobre o cursor economiza memória, pois não carrega tudo de uma vez
    for (text_content,) in cursor:
        if text_content:
            # Limpeza básica de caracteres (ainda em String)
            if clean == '1':
                text_content = Document_Cleansing(text_content)
            
            # Tokenização (Transforma em lista)
            tokens = nltk.word_tokenize(text_content)
            
            # Filtragem de Stopwords e palavras curtas (Já em tokens)
            if clean == '1':
                tokens = [w for w in tokens if w not in stop_words and len(w) > 2]

            array_df.append(tokens)

    cursor.close()
    connection.close()

def get_wordnet_pos(treebank_tag):
    """Converte as tags do NLTK para o formato que o Lemmatizer entende."""
    if treebank_tag.startswith('J'):
        return wordnet.ADJ
    elif treebank_tag.startswith('V'):
        return wordnet.VERB
    elif treebank_tag.startswith('N'):
        return wordnet.NOUN
    elif treebank_tag.startswith('R'):
        return wordnet.ADV
    else:
        return wordnet.NOUN

def lemmatization(texts):
    """Lematização robusta usando NLTK e Part-of-Speech tagging."""
    lemmatizer = WordNetLemmatizer()
    output = []
    for text in texts:
        # Identifica a categoria gramatical de cada palavra (verbo, substantivo...)
        tagged_tokens = nltk.pos_tag(text)
        # Lematiza baseando-se na categoria (ex: 'running' vira 'run' se for verbo)
        lemmatized_doc = [lemmatizer.lemmatize(word, get_wordnet_pos(tag)) for word, tag in tagged_tokens]
        output.append(lemmatized_doc)
    return output

if(lemma == '1'):
    array_df = lemmatization(array_df)

# corpora.Dictionary elimina palavras repitidas
dictionary = corpora.Dictionary(array_df)
# doc2bow é um método do Dictionary que converte uma lista em  BoW
corpus = [dictionary.doc2bow(doc) for doc in array_df]

model = None
excel_file = ""

try:
    if(typeModeling == '1'):
        excel_file = "lda.xlsx"
        model = gensim.models.ldamodel.LdaModel(corpus=corpus,
                                                id2word=dictionary,
                                                num_topics=int(topics),
                                                random_state=100,
                                                update_every=1,
                                                chunksize=10,
                                                passes=int(interaction),
                                                alpha="auto"
                                                )
    elif(typeModeling == '2'):
        # Tipo LSA (LSI)
        excel_file = "lsa.xlsx"
        # No LSI, 'interaction' (passes no LDA) pode ser mapeado para 'power_iters'
        model = gensim.models.lsimodel.LsiModel(corpus=corpus,
                                                id2word=dictionary,
                                                num_topics=int(topics),
                                                power_iters=int(interaction)
                                                )
    elif(typeModeling == '3'):
        # Tipo Word2Vec
        excel_file = "word2vec.xlsx"
        json_file = "word2vec_viz.json"
        # Word2Vec treina sobre a lista de tokens (array_df)
        model_w2v = Word2Vec(sentences=array_df, 
                             vector_size=100, 
                             window=5, 
                             min_count=1, 
                             workers=3, 
                             epochs=int(interaction))
        
        # Extraímos os vetores e as palavras
        vectors = model_w2v.wv.vectors
        words_list = model_w2v.wv.index_to_key

        # Garantir que a pasta de exportação existe antes de salvar o JSON
        base_dir = os.path.dirname(os.path.abspath(__file__))
        export_dir = os.path.join(base_dir, "..", "exportExcel")
        if not os.path.exists(export_dir): os.makedirs(export_dir)
        
        # Agrupamos as palavras em 'topics' clusters para simular tópicos
        kmeans = KMeans(n_clusters=int(topics), random_state=100, n_init=5)
        kmeans.fit(vectors)
        
        # Redução de dimensionalidade para visualização (t-SNE)
        # Reduzimos de 100 dimensões para 2 (x, y)
        tsne = TSNE(n_components=2, random_state=100, perplexity=min(30, len(words_list)-1))
        vectors_2d = tsne.fit_transform(vectors)

        data = []
        for i in range(int(topics)):
            # Identifica palavras pertencentes ao cluster i
            indices = np.where(kmeans.labels_ == i)[0]
            cluster_words = [words_list[idx] for idx in indices]
            
            # Ordena palavras do cluster por frequência global para dar o "weight"
            weighted = []
            for w in cluster_words:
                token_id = dictionary.token2id.get(w)
                count = dictionary.cfs.get(token_id, 0) if token_id is not None else 0
                weighted.append((w, count))
            
            weighted.sort(key=lambda x: x[1], reverse=True)
            
            for word_val, weight_val in weighted[:int(words)]:
                data.append([i, word_val, weight_val])
        
        # Gerar JSON para o Scatter Plot
        viz_data = []
        for idx, word in enumerate(words_list):
            viz_data.append({
                "word": word,
                "x": float(vectors_2d[idx][0]),
                "y": float(vectors_2d[idx][1]),
                "cluster": int(kmeans.labels_[idx])
            })
        
        viz_path = os.path.join(export_dir, json_file)
        with open(viz_path, 'w') as f:
            json.dump(viz_data, f)

        # model_flag para indicar que o processamento manual foi feito
        model = True

    if model and typeModeling in ['1', '2']:
        # show_topics retorna o mesmo formato para ambos os modelos
        topics_extracted = model.show_topics(num_topics=int(topics), num_words=int(words), formatted=False)

        data = []
        for topic_id, topic_words in topics_extracted:
            for word, weight in topic_words:
                data.append([topic_id, word, weight])

    if data:
        df_export = pd.DataFrame(data, columns=["topic", "word", "weight"])
        # Ajuste para caminho relativo ao script
        if typeModeling != '3': # Pasta já criada no bloco Word2Vec se necessário
            base_dir = os.path.dirname(os.path.abspath(__file__))
            export_dir = os.path.join(base_dir, "..", "exportExcel")
            if not os.path.exists(export_dir): os.makedirs(export_dir)

        output_path = os.path.join(export_dir, excel_file)
        
        df_export.to_excel(output_path, index=False)
        print(output_path) # O PHP lê esta linha para confirmar o sucesso

except Exception as e:
    print(f"Erro no processamento {excel_file}: {str(e)}")
    exit()