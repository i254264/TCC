# Estrutura e Fluxo de Funcionamento - TopicGeneration

Este documento apresenta a especificação técnica do módulo de **Geração e Modelagem de Tópicos** do projeto de TCC. A arquitetura une uma interface web responsiva, processamento assíncrono com PHP para comunicação com o banco de dados e scripts robustos em Python (utilizando a biblioteca *Gensim*) para Processamento de Linguagem Natural (PLN) e Modelagem de Tópicos.

---

## 1. Visão Geral da Arquitetura
O sistema adota uma arquitetura em camadas estruturada da seguinte forma:
1. **Frontend (Apresentação):** HTML5, CSS3 estruturado de forma modular e JavaScript Vanilla/jQuery para interações dinâmicas e renderização de gráficos (`Chart.js`).
2. **Backend de Integração (PHP):** Camada responsável por gerenciar uploads, validar estados do banco de dados relacional e disparar processos em segundo plano.
3. **Camada de Dados (MySQL):** Armazenamento estruturado dos textos e metadados importados dos arquivos submetidos pelo usuário.
4. **Motor de Inteligência Artificial (Python 3):** Algoritmos avançados de PLN, Lematização, Engenharia de Recursos e Modelagem Estatística de Tópicos.

---

## 2. Upload de Arquivos e Armazenamento no Banco de Dados

O pipeline de ingestão de dados foi projetado para ser flexível e tolerante a falhas, aceitando formatos estruturados e semiestruturados.

### Requisitos e Estrutura do Banco de Dados
Antes do funcionamento do sistema, é necessária a existência de um banco de dados MySQL com o nome de `topicgeneration`. Esse banco de dados precisa conter as seguintes tabelas estruturais de suporte:

1. **`tabela_topicgeneration`**: Tabela principal para armazenamento definitivo dos metadados textuais extraídos que serão submetidos aos algoritmos de modelagem de tópicos.
2. **`auxiliar`**: Tabela auxiliar temporária para fins de manipulação, filtragem e transição de colunas textuais.

As queries SQL para a criação da base de dados e suas tabelas estruturais são apresentadas a seguir:

```sql
CREATE DATABASE IF NOT EXISTS `topicgeneration`;
USE `topicgeneration`;

CREATE TABLE IF NOT EXISTS `tabela_topicgeneration` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` TEXT DEFAULT NULL,
  `year` VARCHAR(10) DEFAULT NULL,
  `abstract` TEXT DEFAULT NULL,
  `col` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `auxiliar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `col` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Formatos Suportados
*   **CSV (Comma-Separated Values):** Para dados tabulares gerais.
*   **BibTeX (.bib) & RIS (.ris):** Para importação de metadados acadêmicos e referências bibliográficas.

### O Fluxo de Upload (`upload_files.js` -> `saveFile.php`)
1.  **Arrastar ou Selecionar:** O usuário interage com a área de drop (`#drop`). O JavaScript intercepta o arquivo e realiza uma pré-validação de extensão no lado do cliente.
2.  **Envio via AJAX:** Os arquivos são transmitidos de forma assíncrona usando o objeto `FormData` para o backend `saveFile.php`.
3.  **Verificação de Estado do Banco de Dados (`BDexist.php`):**
    *   O PHP verifica se a tabela principal (`tabela_topicgeneration`) já possui registros.
    *   Caso positivo, o sistema entra em estado de **Aviso (Warning)** e pergunta ao usuário no frontend se deseja **Continuar** (concatenar os dados) ou **Deletar** (limpar a base e criar uma nova).
4.  **Criação Dinâmica de Colunas (`createDB.php`):**
    *   O primeiro arquivo do upload é aberto temporariamente para leitura do cabeçalho.
    *   Os nomes das colunas são sanitizados (remoção de caracteres especiais, aspas e substituição de espaços por sublinhados `_`).
    *   Uma instrução SQL `CREATE TABLE IF NOT EXISTS` é executada dinamicamente, gerando as colunas correspondentes no banco de dados.
5.  **Otimização de Transação em Lote:**
    *   Para garantir alta performance ao processar grandes volumes de dados (milhares de linhas), o arquivo PHP inicia uma transação no banco de dados (`$conn->beginTransaction()`).
    *   O parser realiza a leitura sequencial das linhas e executa inserções em lote, realizando o **Commit a cada 1.000 registros gravados**. Isso otimiza o uso de memória e evita gargalos de I/O no MySQL.

---

## 3. Pré-processamento e Modelagem de Tópicos (Python)

Quando o usuário decide gerar o modelo na tela de `topic_modeling.html`, o formulário envia os parâmetros para `php/modeling.php`, que executa o script Python `processingModeling.py` via linha de comando, passando os argumentos de configuração.

### Parâmetros de Entrada
*   **Clean Corpus (Limpeza):** Flag (0 ou 1) para aplicar filtros de ruído textual.
*   **Lemmatize Corpus (Lematização):** Flag (0 ou 1) para reduzir palavras à sua forma raiz baseada na classe gramatical.
*   **Topics/Clusters:** Quantidade desejada de tópicos ou clusters a serem gerados.
*   **Words:** Quantidade de palavras-chave extraídas por tópico para o relatório.
*   **Interaction/Epochs:** Quantidade de iterações/passadas que o modelo executará durante o treino.
*   **Type of Modeling:** Inteiro identificador do modelo:
    *   `1` = Latent Dirichlet Allocation (LDA)
    *   `2` = Latent Semantic Analysis (LSA)
    *   `3` = Word2Vec (W2V)

### Pipeline de PLN no Python
O Python realiza uma conexão local ao banco de dados MySQL, identifica dinamicamente a primeira coluna textual (ignorando a chave primária `id`) e extrai as strings em modo streaming (linha a linha) para economizar memória RAM.

1.  **Limpeza de Texto (`Document_Cleansing`):**
    *   Conversão para caixa baixa (*lowercase*).
    *   Remoção de quebras de linha (`\n`).
    *   Remoção de números complexos, pontuações e caracteres especiais, mantendo apenas letras e espaços.
2.  **Tokenização:** Divisão da string textual bruta em unidades de palavras (tokens) individuais via `nltk.word_tokenize`.
3.  **Filtro de Stopwords:** Remoção de termos funcionais sem valor semântico (como preposições e artigos: *the, is, at, which...*) utilizando a lista oficial da biblioteca NLTK, além de descartar palavras curtas com menos de 3 caracteres.
4.  **Lematização por Categoria Gramatical (*POS-Tagging*):**
    Ao contrário de algoritmos simples de *Stemming* que cortam o final das palavras de forma abrupta, o sistema utiliza o `WordNetLemmatizer` acoplado ao `nltk.pos_tag`. Isso permite identificar se uma palavra é um Verbo, Substantivo, Adjetivo ou Advérbio antes de reduzi-la. 
    *   *Exemplo:* O verbo "running" é mapeado como verbo (`v`) e reduzido corretamente para "run", enquanto o substantivo "universities" é reduzido para "university".

---

## 4. Algoritmos de Modelagem e Estrutura de Saída

### A. Latent Dirichlet Allocation (LDA)
*   **Funcionamento:** É um modelo probabilístico generativo baseado na premissa de que cada documento é uma mistura de vários tópicos e cada tópico é uma distribuição probabilística sobre palavras.
*   **Saída de Dados:** Gera um arquivo Excel chamado `lda.xlsx` contendo as colunas `[topic, word, weight]`, onde `weight` indica a probabilidade matemática da palavra pertencer àquele tópico.

### B. Latent Semantic Analysis (LSA)
*   **Funcionamento:** Utiliza a Decomposição em Valores Singulares (SVD) sobre uma matriz de termos-documentos para identificar padrões nas relações entre termos, reduzindo a dimensionalidade e encontrando conceitos latentes no texto.
*   **Saída de Dados:** Gera o arquivo `lsa.xlsx` contendo as relações de tópicos extraídas.

### C. Word2Vec (Semântica Vetorial)
*   **Funcionamento:** Redes neurais de duas camadas treinam vetores contínuos de palavras. Palavras que compartilham contextos semelhantes na base de dados original são posicionadas próximas no espaço vetorial multidimensional.
*   **Agrupamento (K-Means):** Como o Word2Vec gera apenas vetores contínuos e não tópicos diretos, o script Python aplica o algoritmo **K-Means Clustering** sobre os vetores gerados para agrupar as palavras semanticamente mais próximas em clusters (simulando "tópicos" conceituais).
*   **Redução de Dimensionalidade (t-SNE):** Para viabilizar a visualização em uma tela 2D comum, o algoritmo **t-SNE (t-Distributed Stochastic Neighbor Embedding)** reduz os vetores de 100 dimensões para apenas 2 coordenadas cartesianas $(x, y)$.
*   **Saída de Dados:**
    *   `word2vec.xlsx`: Tabela de agrupamento das palavras com maior relevância/frequência interna de cada cluster.
    *   `word2vec_viz.json`: Estrutura JSON com o mapeamento completo das palavras, suas respectivas coordenadas reduzidas $x$ e $y$, e o identificador do cluster ao qual pertencem.

---

## 5. Interface Gráfica de Exibição de Resultados

A exibição dos dados pós-processamento ocorre dentro do arquivo `topic_modeling.html` usando estilos avançados do `topic-modeling.css` e manipulação do DOM através do `charts_topic_modeling.js`.

### Renderização por Modelo
*   **Visualização LDA e LSA:** Apresenta os tópicos em formato de **Cards Individuais**. Para cada palavra-chave do tópico, é desenhada uma barra de progresso horizontal dinamicamente cuja largura representa o peso estatístico do termo naquele conceito.
*   **Visualização Word2Vec:** Renders um gráfico interativo de dispersão (*Scatter Plot*) utilizando a biblioteca **Chart.js** alimentado pelas coordenadas geradas pelo t-SNE.
    *   Cada ponto no gráfico representa uma palavra.
    *   Os pontos são coloridos automaticamente de acordo com o agrupamento de clusters do K-Means.
    *   Uma lista lateral dinâmica é construída para que o usuário possa ler de forma agrupada as palavras de cada cluster sem a necessidade de passar o mouse em cada ponto individualmente.
---
Documento desenvolvido e estruturado como especificação de software para o projeto de TCC.