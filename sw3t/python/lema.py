import sys
import mysql.connector
from mysql.connector import Error
from textblob import TextBlob, Word
import re

#  preciso instalar os pacotes abaixo: pip install mysql-connector-python==8.0.29
# pip install nltk
# python -m textblob.download_corpora (caso esse comana o download use o seguir)
# python -m pip install -U textblob

#Importando o nltk, faa dowload ao menos 1 vez dos pacotes abaixo:
import nltk
#nltk.download('punkt')
# nltk.download('averaged_perceptron_tagger')
# nltk.download('wordnet')
# nltk.download('omw-1.4')

try:
    connection = mysql.connector.connect(host='localhost',
                                         database='sw3t',
                                         user='root',
                                         password='')
    if connection.is_connected():
        db_Info = connection.get_server_info()
        print("Connected to MySQL Server version ", db_Info)
        cursor = connection.cursor()
        cursor.execute("select database();")
        record = cursor.fetchone()
        print("Your connected to database: ", record)
except Error as e:
    print("Error while connecting to MySQL", e)
    exit()

connection.autocommit = True

#funcao que ser chamada para lematizar resumos
def lemmatize_with_postag(resumo):
    sent = TextBlob(resumo)
    tag_dict = {"J": 'a',
                "N": 'n',
                "V": 'v',
                "R": 'r'}
    words_and_tags = [(w, tag_dict.get(pos[0], 'n')) for w, pos in sent.tags]
    lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]

    lemmatized_listExport = ""
    for word in lemmatized_list:
        if (len(word) > 2):
            lemmatized_listExport = lemmatized_listExport + " " + word

    return lemmatized_listExport

#sys.argv captura parmetros do sistema
# argv[0]  o caminho do prprio arquivo
# argv[1] o prximo parmetro passado
# O comando que chamou o arquivo 'lema.py' foi "["python C:\xampp\htdocs\sw3t\python\lema.py $idProjeto"
idProjeto = sys.argv[1]
idProjeto = re.sub("[^0-9]","",idProjeto)

cursor = connection.cursor()
sql = "SELECT id, resumoEnxuto, resumoLematizado, resumo, tituloArtigo FROM resumo WHERE status = 'incluido' AND idProjeto = %s"
print(sql)
cursor.execute(sql, (idProjeto,))
#captura todas as linhas retornadas do select
records = cursor.fetchall()
#corta conexo com banco
cursor.close()
#tenta fazer UPDATE no banco na coluna de resumo lematizado
try:
    for row in records:
        idResumo = row[0]
        resumo = row[1] or row[3] or row[4] or ""
        resumoLematizadoAtual = row[2] or ""
        print(idResumo)

        if resumoLematizadoAtual.strip():
            continue
        if str(resumo).strip() == "":
            continue

        resumo_lemmatized = lemmatize_with_postag(resumo)
        cursor = connection.cursor()
        cursor.execute("UPDATE `resumo` SET `resumoLematizado` = %s WHERE `resumo`.`id` = %s", (resumo_lemmatized, idResumo))
        cursor.close()

except Error as e:
    print("Error while lemmatizing", e)
    exit()
