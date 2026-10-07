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
*   **Topics/Key Terms:** Quantidade de tópicos desejados (LDA/LSA) ou termos-chave centrais de exploração (Word2Vec).
*   **Words:** Quantidade de palavras-chave extraídas por tópico para o relatório.
*   **Interaction:** Quantidade de iterações/passadas que o modelo executará durante o treino (mapeado para `passes` no LDA, `power_iters` no LSA e `epochs` no Word2Vec).
*   **Type of Modeling:** Inteiro identificador do modelo:
    *   `1` = Latent Dirichlet Allocation (LDA)
    *   `2` = Latent Semantic Analysis (LSA)
    *   `3` = Word2Vec (W2V)

### Pipeline de PLN no Python
O Python realiza uma conexão local ao banco de dados MySQL, identifica dinamicamente a primeira coluna diferente de `id` (sem validação explícita de tipo de dado) e extrai os registros em modo streaming (linha a linha) para economizar memória RAM.

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

Embora todos os modelos sejam executados através da biblioteca *Gensim*, há uma distinção conceitual e matemática relevante entre eles:

* **LSA e LDA (Modelagem baseada em Bag-of-Words):** Atuam a nível de documento, desconsiderando a ordem sequencial das palavras e avaliando matrizes de coocorrência termo-documento (`doc2bow`). O LSA extrai componentes latentes, enquanto o LDA estima distribuições probabilísticas de tópicos.
* **Word2Vec (Representação Vetorial e Semântica de Janela Local):** Atua a nível de sentença/sequência textual, preservando a vizinhança local através de uma janela deslizante (`window`). O Word2Vec aprende vetores densos em $\mathbb{R}^{100}$ para cada termo, viabilizando a análise de relações e vizinhança semântica através do cálculo direto da **similaridade de cosseno** entre as palavras.

| Característica | LSA (Latent Semantic Analysis) | LDA (Latent Dirichlet Allocation) | Word2Vec (Continuous Bag-of-Words) |
| :--- | :--- | :--- | :--- |
| **Paradigma** | Álgebra Linear / Fatoração Matricial | Estatística Bayesiana Probabilística | Redes Neurais Rasas / Otimização por Gradiente |
| **Mecanismo Central** | Decomposição em Valores Singulares (**SVD**) sobre matriz termo-documento. | Inferência Variacional com distribuições *a priori* de **Dirichlet**. | Treinamento de rede neural de 2 camadas com amostragem negativa (**Negative Sampling**). |
| **Escopo de Contexto** | Global (Coocorrência ao nível de documento). | Global (Distribuições probabilísticas no documento). | Local (Janela de palavras vizinhas deslizante). |
| **Saída Original** | Coordenadas contínuas em espaço latente reduzido. | Probabilidade do termo pertencer a cada tópico. | Vetor denso de números reais ($\mathbb{R}^{100}$) e similaridade de cosseno. |
| **Identificação de Tópicos / Relações**| Nativa (`model.show_topics()`). | Nativa (`model.show_topics()`). | Exploração semântica por palavras-chave centrais (`model.wv.most_similar()`). |

### A. Latent Dirichlet Allocation (LDA)
*   **Funcionamento:** É um modelo probabilístico generativo baseado na premissa de que cada documento é uma mistura de vários tópicos e cada tópico é uma distribuição probabilística sobre palavras.
*   **Saída de Dados:** Gera um arquivo Excel chamado `lda.xlsx` contendo as colunas `[topic, word, weight]`, onde `weight` indica a probabilidade de ocorrência da palavra dado o tópico, $P(w \mid z)$.

### B. Latent Semantic Analysis (LSA)
*   **Funcionamento:** Utiliza a Decomposição em Valores Singulares (SVD) sobre uma matriz de termos-documentos para identificar padrões nas relações entre termos, reduzindo a dimensionalidade e encontrando conceitos latentes no texto.
*   **Saída de Dados:** Gera o arquivo `lsa.xlsx` contendo as relações de tópicos extraídas.

### C. Word2Vec e Exploração Semântica por Similaridade de Cosseno
*   **Funcionamento e Configuração do Modelo (`gensim.models.Word2Vec`):**
    O modelo é parametrizado com as seguintes propriedades explícitas e arquiteturais:
    *   `sentences=array_df`: Coleção sequencial de sentenças e tokens pré-processados.
    *   `vector_size=100`: Cada palavra do vocabulário é mapeada em um vetor denso de 100 dimensões.
    *   `window=5`: Alcance máximo de contexto para predição (5 termos à esquerda e 5 à direita).
    *   `min_count=2`: Descarta palavras com menos de duas ocorrências no corpus.
    *   `workers=3`: Execução multithreaded para aceleração via rotinas em Cython.
    *   `epochs=interaction`: Número de passadas completas de otimização sobre a base de textos.
    *   **Padrões Implícitos da Gensim Ativos:**
        *   *Arquitetura CBOW (`sg=0`)*: Utiliza o Continuous Bag-of-Words, prevendo a palavra central com base na média dos vetores das palavras vizinhas (`cbow_mean=1`).
        *   *Negative Sampling (`hs=0`, `negative=5`)*: Aplica amostragem de ruído (5 amostras negativas por etapa) para aproximação eficiente da função de perda logística, dispensando a complexidade da Hierarchical Softmax.
        *   *Taxa de Aprendizado Linear*: Varia de `alpha=0.025` decaindo até `min_alpha=0.0001`.
*   **Uso de `KeyedVectors`:**
    Após o treinamento, o script extrai diretamente as relações vetoriais através de `model.wv`:
    *   **Seleção de Palavras-Chave Centrais (*Key Terms*):** O vocabulário é ordenado pela contagem total de ocorrências (`model.wv.get_vecattr(w, "count")`), selecionando as $N$ palavras mais frequentes como referências do corpus.
    *   **Similaridade de Cosseno com os Vizinhos:** A função nativa `model.wv.most_similar(key_term, topn=num_similar)` calcula a similaridade de cosseno entre o vetor da palavra semente e os demais termos do vocabulário no espaço $\mathbb{R}^{100}$, retornando os $N$ termos com maior score:
        $$\cos(\theta) = \frac{\mathbf{u} \cdot \mathbf{v}}{\|\mathbf{u}\| \|\mathbf{v}\|}$$
*   **Saída de Dados:**
    *   `word2vec.xlsx`: Planilha contendo `[topic, word, weight]`, onde a coluna `topic` identifica a palavra semente analisada (`Keyword 0: 'termo'`), `word` são os vizinhos semânticos e `weight` é o score de similaridade de cosseno.
    *   `w2v_viz.json`: Estrutura JSON serializada contendo a mesma tríade `[topic, word, weight]`, permitindo a padronização completa de renderização no frontend.

---

## 5. Interface Gráfica de Exibição de Resultados

A exibição dos dados pós-processamento ocorre dentro do arquivo `topic_modeling.html` usando estilos avançados do `topic-modeling.css` e manipulação do DOM através do `charts_topic_modeling.js`.

### Visualização Padronizada em Cards e Barras de Relevância
Todos os modelos utilizam uma renderização unificada (`renderTopicGroups`), garantindo coerência visual e facilidade de comparação didática:
*   **Cabeçalho Geral Dinâmico:** Para LDA e LSA, a seção é intitulada como *"[MODELO] Topic Groups & Keyword Weights"*; para o Word2Vec, passa a ser identificada como *"W2V Word Similarity & Keyword Weights"*, diferenciando tópicos latentes de listas de proximidade semântica.
*   **Título Unificado dos Cards:** Todos os agrupamentos são identificados pelo cabeçalho `Group Keyword: "..."`, eliminando índices numéricos (`Topic 0`, `Keyword 1`) para evitar falsa hierarquia ou ordenação prioritária entre os agrupamentos.
*   **LDA e LSA:** No LDA, o título do card destaca o termo com maior probabilidade ($P(w \mid z)$); no LSA, destaca o termo de maior magnitude absoluta ($|w|$), e não necessariamente o maior valor positivo.
*   **Word2Vec:** O título do card destaca a palavra semente de referência (*Group Keyword: "seed"*), enquanto a lista interna apresenta os $N$ termos mais similares encontrados no corpus.
*   **Exportação:** Em qualquer um dos três modelos, o botão de download dinâmico disponibiliza o arquivo Excel correspondente gerado pelo processamento em Python.

### Significado Teórico dos Pesos Brutos e Barras nos Cards
No frontend (`charts_topic_modeling.js`), o valor numérico exibe o peso bruto com seu sinal original. A ordenação da lista e a escala da barra de progresso adaptam-se à natureza matemática de cada modelo, calculadas sempre em relação aos termos exibidos no próprio card (com um piso de segurança de $0{,}00001$ no denominador para prevenir divisões por zero):

1. **LDA (Latent Dirichlet Allocation):**
   * **Peso Bruto (`weight`):** Probabilidade condicional $P(w \mid z)$ de ocorrência da palavra dado o tópico.
   * **Ordenação e Barra:** Ordenado de forma decrescente pela probabilidade. A barra representa a proporção normalizada em relação ao termo de maior probabilidade entre os termos exibidos ($\frac{w_i}{\max_j w_j} \times 100$).
2. **LSA (Latent Semantic Analysis):**
   * **Peso Bruto (`weight`):** Carga ou projeção estatística no vetor singular latente resultante do SVD (podendo assumir valores positivos ou negativos).
   * **Ordenação e Barra:** Ordenado pela magnitude absoluta decrescente ($|w|$). A barra mede a magnitude relativa em relação ao termo de maior carga absoluta entre os termos exibidos ($\frac{|w_i|}{\max_j |w_j|} \times 100$).
3. **Word2Vec (W2V):**
   * **Peso Bruto (`weight`):** Score de similaridade de cosseno ($\cos\theta \in [-1.0, 1.0]$) entre os vetores da palavra vizinha e da palavra semente.
   * **Ordenação e Barra:** Ordenado pelo valor real decrescente do cosseno. A barra máxima corresponde ao maior resultado positivo entre os termos exibidos ($\frac{w_i}{\max_j w_j} \times 100$); valores não positivos recebem barra zerada ($0\%$). Caso todos os resultados retornados no grupo sejam negativos ou nulos, todas as barras permanecem em $0\%$.

### Guia Interativo de Apoio ao Usuário (*Modeling Guide*)
Para democratizar a análise e permitir que pesquisadores sem formação aprofundada em Álgebra Linear ou Estatística Bayesiana interpretem os modelos, a interface integra um modal de auxílio em abas (`#popUP`):
* **Abas Contextuais:** Divisão dedicada entre LSA, LDA e Word2Vec com explicações didáticas dos algoritmos.
* **Glossário Didático de Parâmetros:** Tradução prática do impacto de parâmetros como número de tópicos/termos-chave, passadas de treinamento (*interaction*), limpeza (*clean corpus*) e lematização (*lemmatize corpus*).
* **Interpretação Descomplicada das Saídas:**
  * **LSA:** O valor numérico é apresentado como *Strength Score* (força de ancoragem do termo no tema).
  * **LDA:** O valor numérico é interpretado como *Topic Probability* (chance de ocorrência na discussão temática).
  * **Word2Vec:** O valor numérico é traduzido como *Context Similarity* (similaridade entre os vetores aprendidos a partir dos contextos das palavras).
  * **Porcentagem (%):** Apresentada como magnitude relativa em relação ao termo de maior valor exibido no grupo (normalizado em 100%). No LDA e LSA, esse termo coincide com a palavra líder do componente; no Word2Vec, o título destaca a palavra semente consultada, enquanto a barra de 100% corresponde ao vizinho mais similar retornado.
---
Documento desenvolvido e estruturado como especificação de software para o projeto de TCC.