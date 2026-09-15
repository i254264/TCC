
import os
# print(os.environ['PATH'])
# exit;
# os.environ['PATH'] =  '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/snap/bin'


import nltk
try:
    from nltk.corpus import stopwords
except LookupError:
    nltk.download('stopwords', quiet=True)
    from nltk.corpus import stopwords
# from nltk.stem.wordnet import WordNetLemmatizer
# from nltk.stem import WordNetLemmatizer 
import gensim
from gensim import corpora
from gensim.models import CoherenceModel
# from gensim.models import LdaModel
import string

import numpy as np

import sys #Para receber parametros do terminal :P
#import os
import re #para sanitizar o paremetro recebido :D

import json #para exportar bonitinho para o banco de dados
import hashlib #para fazer o nome do arquivo
import time
#Precisamos do myql :D
import mysql.connector
from mysql.connector import Error

import pandas as pd
# pip install textblob
# from textblob import TextBlob, Word

# import pyLDAvis.gensim
import pyLDAvis.gensim_models

if(sys.argv[9] == "0"):
    SOME_FIXED_SEED = 1337
    np.random.seed(SOME_FIXED_SEED)
# SOME_FIXED_SEED = 1337
# np.random.seed(SOME_FIXED_SEED)

try:
    connection = mysql.connector.connect(host='localhost',
                                         database='sw3t',
                                         user='root',
                                         password='')
    if connection.is_connected():
        db_Info = connection.get_server_info()
        # print("Connected to MySQL Server version ", db_Info)
        # cursor = connection.cursor()
        # cursor.execute("select database();")
        # record = cursor.fetchone()
        # print("Your connected to database: ", record)
except Error as e:
    print("Error while connecting to MySQL", e)
    exit()


connection.autocommit=True

# def log_perplexity(self, chunk, total_docs=None):
        # """Calculate and return per-word likelihood bound, using a chunk of documents as evaluation corpus.
        # Also output the calculated statistics, including the perplexity=2^(-bound), to log at INFO level.
        # Parameters
        # ----------
        # chunk : {list of list of (int, float), scipy.sparse.csc}
            # The corpus chunk on which the inference step will be performed.
        # total_docs : int, optional
            # Number of docs used for evaluation of the perplexity.
        # Returns
        # -------
        # numpy.ndarray
            # The variational bound score calculated for each word.
        # """
        # if total_docs is None:
            # total_docs = len(chunk)
        # corpus_words = sum(cnt for document in chunk for _, cnt in document)
        # subsample_ratio = 1.0 * total_docs / len(chunk)
        # perwordbound = self.bound(chunk, subsample_ratio=subsample_ratio) / (subsample_ratio * corpus_words)
        # logger.info(
            # "%.3f per-word bound, %.1f perplexity estimate based on a held-out corpus of %i documents with %i words",
            # perwordbound, np.exp2(-perwordbound), len(chunk), corpus_words
        # )
        # return perwordbound




	
idProjeto = sys.argv[1]
idProjeto = re.sub("[^0-9]", "", idProjeto) #sanitizando o idProjeto >:/
idTrabalho = sys.argv[2]
idTrabalho = re.sub("[^0-9]", "", idTrabalho) 


qtdTopicos = sys.argv[3]
qtdTopicos = re.sub("[^0-9]", "", qtdTopicos) 
qtdTopicos = int(qtdTopicos);


qtdPalavras = sys.argv[4]
qtdPalavras = re.sub("[^0-9]", "", qtdPalavras) 
qtdPalavras = int(qtdPalavras);


qtdRounds = sys.argv[5]
qtdRounds = re.sub("[^0-9]", "", qtdRounds) 
qtdRounds = int(qtdRounds);

inicioAno = sys.argv[6]
inicioAno = re.sub("[^0-9]", "", inicioAno) 
inicioAno = int(inicioAno);
fimAno = sys.argv[7]
fimAno = re.sub("[^0-9]", "", fimAno) 
fimAno = int(fimAno);


grafico = sys.argv[8]
#pip install mysql-connector-python

# remover mais palavras, criando uma lista de palavras a serem removidas

areaConhecimento = sys.argv[10]

meuStopWordDb=[];

cursor = connection.cursor()
cursor.execute("select palavra from stopwords where idProjeto=" + idProjeto)
records = cursor.fetchall()
cursor.close()
for row in records:
	#meuStopWordDb=row[0] + "\n" +meuStopWord1 #tem que ver como ele le os stopwords, qualquer coisa deh um split
	meuStopWordDb.append(row[0]) #tem que ver como ele le os stopwords, qualquer coisa deh um split



meuStopWord = meuStopWordDb
# with open("meuStopWordsT.txt", "rb") as msw:
	# meuStopWord = msw.read().decode('utf-8').split("\r\n")


###PARTE 1
#doc1 = "Sugar is bad to consume. My sister likes to have sugar, but not my father."
#doc2 = "My father spends a lot of time driving my sister around to dance practice."
#doc3 = "Doctors suggest that driving may cause increased stress and blood pressure."
#doc4 = "Sometimes I feel pressure to perform well at school, but my father never seems to drive my sister to do better."
#doc5 = "Health experts say that Sugar is not good for your lifestyle."

# compile documents
#doc_complete = [doc1, doc2, doc3, doc4, doc5]

###PARTE 2
stop = set(stopwords.words('english'))
exclude = set(string.punctuation) 
# lemma = WordNetLemmatizer()

# def parse_xls(xls_file=None):
	# conjuntoAbs = []

	# if not xls_file:
		# return False

	# from openpyxl import load_workbook
    
	# workbook = load_workbook(xls_file, read_only=True)
	# first_sheet = workbook.get_sheet_names()[0]
	# worksheet = workbook.get_sheet_by_name(first_sheet)

	# for x,row in enumerate(worksheet.iter_rows()):
		# if x > 0:
			# try:
				# conjuntoAbs.append(row[10].value)
			# except Exception as ex:
					# pass

	# return conjuntoAbs

def clean(doc):
	#print(doc)
	stop_free = " ".join([i for i in doc.lower().split() if i not in stop])
	punc_free = ''.join(ch for ch in stop_free if ch not in exclude)
	# normalized = " ".join(lemma.lemmatize(word) for word in punc_free.split())
	# meustop_free = " ".join([i for i in normalized.split() if i not in meuStopWord])
	meustop_free = " ".join([i for i in punc_free.split() if i not in meuStopWord])
	return meustop_free


def bigramsF(words):
 
    bigrams = []
 
    for i in range(0, len(words)):
        if (i == len(words)-1):
            break
        else:
            bigrama_obs = words[i] + '_' + words[i+1]
            bigrams.append(bigrama_obs)
 
    return ' '.join(bigrams)

# def trigramsF(words):
 
    # trigrams = []
 
    # for i in range(0, len(words)):
        # if (i == len(words)-2):
            # break
        # else:
            # trigrama_obs = words[i] + '_' + words[i+1] + '_' + words[i+2]
            # trigrams.append(trigrama_obs)
 
    # return ' '.join(trigrams)

#doc_complete = parse_xls("IEEE_248.xlsx")



# lemmatizer = WordNetLemmatizer() #socorro vai dar errado


resumosEnxutosDb=[]
cursor = connection.cursor()
if(areaConhecimento == 'todas'):
    sql = "SELECT `id`,`resumoLematizado`,`resumoEnxuto`,`tituloArtigo`,`resumo` FROM `resumo` WHERE status = 'incluido' AND `idProjeto` = " + str(idProjeto) + " AND anoPublicacao >= " + str(inicioAno) + " AND anoPublicacao <= " + str(fimAno)
else:
    sql = "SELECT `id`,`resumoLematizado`,`resumoEnxuto`,`tituloArtigo`,`resumo` FROM `resumo` WHERE status = 'incluido' AND `idProjeto` = " + str(idProjeto) + " AND anoPublicacao >= " + str(inicioAno) + " AND anoPublicacao <= " + str(fimAno) + " AND `area_conhecimento` =" + "'"+str(areaConhecimento)+"'"
# print(sql)
# exit()
cursor.execute(sql)
records = cursor.fetchall()
cursor.close()






# def lemmatize_with_postag(sentence):
    # sent = TextBlob(sentence)
    # tag_dict = {"J": 'a', 
                # "N": 'n', 
                # "V": 'v', 
                # "R": 'r'}
    # words_and_tags = [(w, tag_dict.get(pos[0], 'n')) for w, pos in sent.tags]    
    # lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]
    # return " ".join(lemmatized_list)



resumosEnxutosDbId =[]
for row in records:
    idResumo = row[0]
    textoBase = row[1] or row[2] or row[4] or row[3] or ""
    if str(textoBase).strip() == "":
        continue

    resumoLema = clean(str(textoBase))
    if str(resumoLema).strip() == "":
        continue

    resumoLemaNew = resumoLema
    resumoLema = resumoLema.split(' ')
    # resumoLemaNew = resumoLemaNew + ' ' + bigramsF(resumoLema) + ' ' + trigramsF(resumoLema)
    resumoLemaNew = resumoLemaNew + ' ' + bigramsF(resumoLema)
    print(resumoLemaNew)

    resumosEnxutosDbId.append(idResumo)
    resumosEnxutosDb.append(resumoLemaNew)

if len(resumosEnxutosDb) == 0:
    raise ValueError("Nenhum texto válido encontrado para análise. Verifique se o projeto tem resumo/abstract ou título preenchido antes de gerar tópicos.")
	


#doc_clean = [clean(doc).split() for doc in doc_complete]
# doc_clean = [clean(doc[1]).split() for doc in resumosEnxutosDb] # doc[0] tem o id do resumo do meu banco de dados, tem q fazer alguma maneira de receber ele 
# doc_clean = [clean(doc).split() for doc in resumosEnxutosDb] 
doc_clean = [(doc).split() for doc in resumosEnxutosDb] 




# Salvar arquivo limpo
with open("arquivo_limpo.txt","wb") as _file_:
	doc_clean_str = str(doc_clean).encode("utf-8")
	_file_.write(doc_clean_str)  

dictionary = corpora.Dictionary(doc_clean)

###PARTE 3
# Creating the term dictionary of our courpus, where every unique term is assigned an index. dictionary = corpora.Dictionary(doc_clean)
# Converting list of documents (corpus) into Document Term Matrix using dictionary prepared above.
doc_term_matrix = [dictionary.doc2bow(doc) for doc in doc_clean]
# print(doc_term_matrix)
# print("\n")
# print(doc_clean)
# exit()




# Creating the object for LDA model using gensim library
Lda = gensim.models.ldamodel.LdaModel

# Running and Trainign LDA model on the document term matrix.

# ldamodel = Lda(doc_term_matrix, num_topics=10, id2word = dictionary, passes=50)
ldamodel = Lda(doc_term_matrix, qtdTopicos, id2word = dictionary, passes = qtdRounds)
nomeJson = "none"
##############################################################################
##############################################################################
##############################################################################
###########################VISUALIZADOR#######################################

if(grafico == "1"):
	nomeJson = str(fimAno)+str(inicioAno)+str(qtdRounds)+str(qtdPalavras)+str(qtdTopicos)+str(idTrabalho)+str(idProjeto)+str(time.time())
	nomeJson = hashlib.md5(nomeJson.encode())
	nomeJson = "C:/xampp/htdocs/jsons/"+str(nomeJson.hexdigest())+".json"
	movies_vis_data = pyLDAvis.gensim_models.prepare(ldamodel, doc_term_matrix, dictionary, mds='mmds')
	pyLDAvis.save_json(movies_vis_data,nomeJson)


# print(movies_vis_data)
# exit()
##############################################################################
##############################################################################
##############################################################################
##############################################################################
##############################################################################
relacaoArtigos = "1"
#relecaoArtigos = "0"

if(relacaoArtigos == "1"):
    def format_topics_sentences(ldamodel, corpus, texts):
        # Init output
        rows = []

        # Get main topic in each document
        for i, row in enumerate(ldamodel[corpus]):
            row = sorted(row, key=lambda x: (x[1]), reverse=True)
            # Get the Dominant topic, Perc Contribution and Keywords for each document
            for j, (topic_num, prop_topic) in enumerate(row):
                if j == 0:  # => dominant topic
                    wp = ldamodel.show_topic(topic_num)
                    topic_keywords = ", ".join([word for word, prop in wp])
                    rows.append([int(topic_num), round(prop_topic,4), topic_keywords])
                else:
                    break
        sent_topics_df = pd.DataFrame(rows, columns=['Dominant_Topic', 'Perc_Contribution', 'Topic_Keywords'])

        # Add original text to the end of the output
        contents = pd.Series(texts)
        #ids = pd.Series(texts[0])
        sent_topics_df = pd.concat([sent_topics_df, contents], axis=1)
        #sent_topics_df = pd.concat([ids, sent_topics_df], axis = 0)
        return(sent_topics_df)

    df_topic_sents_keywords = format_topics_sentences(ldamodel, doc_term_matrix, resumosEnxutosDbId)

    # Format
    df_dominant_topic = df_topic_sents_keywords.reset_index()
    df_dominant_topic.columns = ['id', 'Dominant_Topic', 'Topic_Perc_Contrib', 'Keywords', 'idResumo']
    # Show
    #df_dominant_topic.head(10)
    #teste = df_dominant_topic[df_dominant_topic.id == 0]
    #teste = teste.values
    #sys.stdout = open(os.devnull, "w")

    teste = df_dominant_topic.values

    #sys.stdout = sys.__stdout__
    
    #teste = df_dominant_topic.to_numpy
    #topico = teste[1]

    sql = ""
    cursor = connection.cursor()
    for val in teste:
        #print(val)
        idResumo = re.escape(str(val[4]))
        idTopicoLda = re.escape(str(val[1]))
        keywordsLda = re.escape(str(val[3]))
        porcentagem = re.escape(str(val[2]))
        sql =   "INSERT INTO `resumotrabalho` ( `idTrabalho`, `idResumo`, `idTopicoLda`, `keywordsLda`, `porcentagem`) VALUES ( '"+str(idTrabalho)+"', '"+idResumo+"', '"+idTopicoLda+"', '"+keywordsLda+"', '"+porcentagem+"');"
        
        cursor.execute(sql)
        #cursor = connection.cursor()
        
        #cursor.close()
    #print(sql)
    cursor.close()
#cursor.execute(sql,multi=True)
#print(sql)

#exit()
    

output = []

words = 10
topics = 10
# words = qtdPalavras
# topics = qtdTopicos

if(qtdPalavras <= 10):
	words = qtdPalavras
if(qtdTopicos <= 10):
	topics = qtdTopicos
# if(qtdPalavras > 10):
	# words = words -1
# if(qtdTopicos > 10):
	# topics = topics -1

# calcular perplexidade
perp = ldamodel.log_perplexity(doc_term_matrix)

# calcular coerencia
# coherence_model = CoherenceModel(model=ldamodel, texts=doc_clean, dictionary=dictionary, coherence='c_v')
# coherence_model = CoherenceModel(model=ldamodel, texts=doc_term_matrix, dictionary=dictionary, coherence='c_v')
# c_v : medida de coerencia que a criatura escolheu usar no outro codigo
# coherence_score = coherence_model.get_coherence() # get coherence value
coherence_score = 0 # get coherence value
coer = str(coherence_score*100)+"%"

# calcular acuracia
table = ldamodel.show_topics(formatted=False)
real = [ldamodel.alpha[0]/words for x in range(words*topics)]

# print(table)
# print(list(range(words)))
# print(list(range(topics)))

predict = [table[x][1][y][1] for y in range(words) for x in range(topics)]
real, predict = np.array(real), np.array(predict)
acc = str((1 - np.mean(np.abs(((real*predict) - (predict**2)) / (real*predict)))) * 100)+"%"
# usando MAPE : np.mean(np.abs((real - predict) / real)) * 100


















# for e in ldamodel.print_topics(num_topics=10, num_words=8):
for e in ldamodel.print_topics(qtdTopicos, qtdPalavras):
	#print(e)
	output.append(e)
	





cursor = connection.cursor()
for resultado in output:
	#resultado = json.dumps(str(resultado))
	resultado = re.escape(str(resultado))
	resultado = resultado.replace("'", "")
	sql = "INSERT INTO `output` (`id`, `idProjeto`, `idTrabalho`, `output`) VALUES (NULL, '"+str(idProjeto)+"', '"+str(idTrabalho)+"',  '"+resultado+"');"
	print(sql)
# 	exit()
	cursor.execute(sql)

cursor.close()




# nomeJson = nomeJson.replace("'", "\'")
cursor = connection.cursor()
sql = "UPDATE `trabalho` SET `status` = 'concluido', coerencia = '"+str(coer)+"',perplexidade = '"+str(perp)+"',acuracia = '"+str(acc)+"', outJson = '"+str(nomeJson)+"' WHERE `trabalho`.`id` = " + str(idTrabalho)
print(sql)
cursor.execute(sql)
cursor.close()

if (connection.is_connected()):
	connection.close()
	#print("MySQL connection is closed")	

 