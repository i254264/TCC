import subprocess
import sys
import os

def install_dependencies():
    """Tenta instalar as dependências básicas caso não existam."""
    packages = [
        "spacy", "pandas", "gensim", "pyLDAvis", 
        "mysql-connector-python", "openpyxl", "nltk"
    ]
    for package in packages:
        try:
            __import__(package.replace('-', '_'))
        except ImportError:
            subprocess.check_call([sys.executable, "-m", "pip", "install", package])

    # Garante o modelo do spaCy
    try:
        import spacy
        spacy.load("en_core_web_sm")
    except (ImportError, OSError):
        subprocess.check_call([sys.executable, "-m", "spacy", "download", "en_core_web_sm"])

install_dependencies()
import nltk

def download_nltk_resources():
    resources = ['stopwords', 'punkt', 'averaged_perceptron_tagger', 'wordnet', 'omw-1.4']
    for res in resources:
        try:
            nltk.data.find(res)
        except LookupError:
            nltk.download(res, quiet=True)

download_nltk_resources()

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

def lemmatization(texts, allowed_postags=["NOUN", "ADJ", "VERB", "ADV"]):
    # Carrega o modelo do spaCy uma única vez para economizar tempo
    try:
        nlp = spacy.load("en_core_web_sm", disable=["parser", "ner"])
    except OSError:
        print("Erro: O modelo 'en_core_web_sm' do spacy nao foi encontrado.")
        print("Tente: python -m spacy download en_core_web_sm")
        sys.exit(1)

    output = []
    for text in texts:
        doc = nlp(" ".join(text))  # Converte a lista de tokens em uma string para processamento
        lemma_abs = []  # Redefine lemma_abs para cada documento
        for token in doc:
            if token.pos_ in allowed_postags:
                lemma_abs.append(token.lemma_)
        output.append(lemma_abs)
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