import sys
import os
import nltk
from nltk.corpus import stopwords
import pandas as pd
import re
#Gensim
import gensim
from gensim import corpora
from nltk.corpus import wordnet
from nltk.stem import WordNetLemmatizer
#vis
import pyLDAvis
import pyLDAvis.gensim_models
import spacy
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

try:
    connection = mysql.connector.connect(host='localhost',
                                         database='topicgeneration',
                                         user='root',
                                         password='')
    if connection.is_connected():
        db_Info = connection.get_server_info()
        cursor = connection.cursor()
        cursor.execute("select database();")
        record = cursor.fetchone()
except Error as e:
    print("Erro SQL")
    exit()

connection.autocommit = True

if connection.is_connected():
    # Executar a consulta SQL para selecionar os dados da coluna desejada
    cursor = connection.cursor()
    
    # Identifica dinamicamente a primeira coluna que não seja 'id'
    cursor.execute("SHOW COLUMNS FROM tabela_topicgeneration")
    columns = [row[0] for row in cursor.fetchall() if row[0].lower() != 'id']
    
    if not columns:
        print("Erro: Nenhuma coluna de dados encontrada.")
        sys.exit(1)
    
    target_col = columns[0] # Assume a primeira coluna de dados como alvo
    cursor.execute(f"SELECT `{target_col}` FROM tabela_topicgeneration")
    
    # Obter todos os resultados da consulta
    results = cursor.fetchall()
    df = pd.DataFrame(results, columns=['text_content'])

    # Fechar o cursor e a conexão
    cursor.close()
    connection.close()

def Document_Cleansing(Document):
    # Verificar se Document é um valor nulo
    if pd.isna(Document):
        return ''  # Retorna uma string vazia para valores nulos
    Document = " ".join([word for word in Document.split() if word not in stop_words])
    Document = " ".join([word for word in Document.split() if len(word) > 2 ])

    # This will make all the words in the documents lower-case:
    Document = Document.lower()

    # removing ambiguous characters
    Document = re.sub(r'[^\w\s]', '', Document)

    # removing numbers which contain commas:
    Document = re.sub(r'(\d+),(\d+),?(\d*)', '',  Document)

    # removing \n terms:
    Document = re.sub(r'(\\n)', '', Document)

    # removing numbers which contain commas:
    Document = re.sub(r'(\d+),(\d+),?(\d*)', " ", Document)

    return Document

if(clean == '1'):
    df['text_content'] = df['text_content'].apply(Document_Cleansing)

df['tokenized'] = df['text_content'].apply(nltk.word_tokenize)
array_df = df['tokenized'].tolist()

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

# Tipo LDA
if(typeModeling == '1'):
    try:
        lda_model = gensim.models.ldamodel.LdaModel(corpus=corpus,
                                            id2word=dictionary,
                                            num_topics= int(topics),
                                            random_state=100, #semente
                                            update_every=1, #frequência que o modelo é atualizado ao ver cada documento
                                            chunksize=10, #número de documentos a serem usados em cada iteração
                                            passes=int(interaction), #número de vezes que o modelo percorrerá o corpus inteiro durante o treinamento
                                            alpha="auto" #distribuição de tópicos por documento
                                            )
        # Visualizar os tópicos gerados pelo modelo LDA
        topics = lda_model.show_topics(num_topics=int(topics), num_words=int(words), formatted=False)

        # Criar um DataFrame
        data = []
        for topic_id, topic_words in topics:
            for word, weight in topic_words:
                data.append([topic_id, word, weight])

        df_export = pd.DataFrame(data, columns=["topic", "word", "weight"])
        excel_file = "lda.xlsx"
        # Ajuste para caminho relativo ao script
        base_dir = os.path.dirname(os.path.abspath(__file__))
        dir = os.path.join(base_dir, "..", "exportExcel")
        if not os.path.exists(dir): os.makedirs(dir)
        output_path = os.path.join(dir, excel_file)
        try:
            df_export.to_excel(output_path, index=False)

            #se tudo der certo, retorna caminho do arquivo
            print(output_path)
        except Error as e:
            print("Erro EXCEL")
            exit()

    except Error as e:
        print("Erro LDA")
        exit()