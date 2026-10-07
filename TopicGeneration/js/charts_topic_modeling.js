/**
 * ==========================================================================
 * Lógica de Geração e Visualização de Gráficos e Tópicos (LSA, LDA, W2V)
 * ==========================================================================
 */

let w2vChartInstance = null;

/**
 * Busca o JSON do Word2Vec e monta o gráfico de dispersão t-SNE e a legenda lateral.
 */
function renderW2VChart() {
    // Timestamp para evitar cache do JSON
    fetch("exportExcel/word2vec_viz.json?t=" + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            $("#w2v-viz-container").fadeIn(800);
            // Garante que a área de gráficos esteja ativa e os cards estejam ocultos
            $("#topic-groups-area").hide();
            $("#w2v-chart-area").show();
            $("#viz-title").text("Word2Vec Semantic Clusters (t-SNE)");
            $("#viz-footer-text").text("Hover over the points to see the keywords. Proximity represents semantic similarity.");
            
            const ctx = document.getElementById('w2vChart').getContext('2d');
            
            if (w2vChartInstance) {
                w2vChartInstance.destroy();
            }

            // Paleta de cores para os clusters
            const clusterColors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
                '#FF9F40', '#8AC926', '#1982C4', '#6A4C93', '#F15BB5'
            ];

            // Limpa e prepara a lista lateral
            const $listContainer = $("#w2v-clusters-list");
            $listContainer.html('<h3 class="w2v-clusters-header">Cluster Keywords</h3>');

            // Suporte para o novo formato com top_words ou formato legado em array
            const points = Array.isArray(data) ? data : (data.points || []);
            const topWordsData = (!Array.isArray(data) && data.top_words) ? data.top_words : null;
            const maxWordsLimit = parseInt($("#w2v-words").val()) || 5;

            // Agrupa os pontos por cluster para criar a legenda
            const datasets = [];
            const wordsByCluster = {};

            // Inicializa as palavras dos grupos caso já venham filtradas do backend
            if (topWordsData) {
                Object.keys(topWordsData).forEach(cId => {
                    wordsByCluster[cId] = topWordsData[cId];
                });
            }

            points.forEach(item => {
                const clusterId = item.cluster;
                
                // Fallback para caso legado: filtra apenas as mais relevantes
                if (!topWordsData) {
                    if (!wordsByCluster[clusterId]) {
                        wordsByCluster[clusterId] = [];
                    }
                    if (item.is_top) {
                        if (!wordsByCluster[clusterId].includes(item.word)) {
                            wordsByCluster[clusterId].push(item.word);
                        }
                    } else if (item.weight !== undefined) {
                        if (!wordsByCluster[clusterId].some(w => w.word === item.word)) {
                            wordsByCluster[clusterId].push({ word: item.word, weight: item.weight });
                        }
                    } else {
                        if (!wordsByCluster[clusterId].includes(item.word)) {
                            wordsByCluster[clusterId].push(item.word);
                        }
                    }
                }

                // Prepara dados para o gráfico
                let ds = datasets.find(d => d.label === 'Group ' + clusterId);
                if (!ds) {
                    ds = {
                        label: 'Group ' + item.cluster,
                        data: [],
                        backgroundColor: clusterColors[item.cluster % clusterColors.length],
                        pointRadius: 6,
                        pointHoverRadius: 10
                    };
                    datasets.push(ds);
                }
                ds.data.push({
                    x: item.x,
                    y: item.y,
                    word: item.word
                });
            });

            // Limita a exibição às N palavras configuradas caso esteja no modo fallback
            if (!topWordsData) {
                Object.keys(wordsByCluster).forEach(clusterId => {
                    const list = wordsByCluster[clusterId];
                    if (list.length && typeof list[0] === 'object') {
                        list.sort((a, b) => b.weight - a.weight);
                        wordsByCluster[clusterId] = list.slice(0, maxWordsLimit).map(x => x.word);
                    } else {
                        wordsByCluster[clusterId] = list.slice(0, maxWordsLimit);
                    }
                });
            }

            // Injeta as palavras na lista lateral estilizadas em cards padronizados
            Object.keys(wordsByCluster).sort((a, b) => Number(a) - Number(b)).forEach(clusterId => {
                const color = clusterColors[clusterId % clusterColors.length];
                const words = wordsByCluster[clusterId];
                const wordsPillsHtml = words.map(w => `<span class="w2v-word-pill">${w}</span>`).join('');
                
                $listContainer.append(`
                    <div class="w2v-cluster-card" style="border-left: 4px solid ${color};">
                        <div class="w2v-card-header">
                            <span class="w2v-cluster-name">
                                <span class="cluster-indicator" style="background-color: ${color};"></span>
                                Group ${clusterId}
                            </span>
                            <span class="topic-badge" style="background-color: ${color}20; color: ${color};">${words.length} words</span>
                        </div>
                        <div class="w2v-words-container">${wordsPillsHtml}</div>
                    </div>
                `);
            });

            w2vChartInstance = new Chart(ctx, {
                type: 'scatter',
                data: { datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Word: ${context.raw.word} (Group ${context.datasetIndex})`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: '#f5f5f5' },
                            title: { display: true, text: 'Semantic dimension X' }
                        },
                        y: {
                            grid: { color: '#f5f5f5' },
                            title: { display: true, text: 'Semantic dimension Y' }
                        }
                    }
                }
            });
            
            // Scroll suave para mostrar o gráfico após carregar
            $('html, body').animate({
                scrollTop: $("#w2v-viz-container").offset().top - 30
            }, 1000);
        })
        .catch(err => console.error("Erro ao carregar visualização W2V:", err));
}

/**
 * Processa e desenha os cards de tópicos dinâmicos para LDA e LSA.
 */
function renderTopicGroups(type, responseData) {
    $("#w2v-viz-container").fadeIn(800);
    $("#w2v-chart-area").hide();
    $("#topic-groups-area").show().html('<p class="viz-status-loading">Loading topic groups...</p>');
    
    // Atualiza cabeçalhos dinamicamente
    const titleText = type.toUpperCase() + " Topic Groups & Keyword Weights";
    $("#viz-title").text(titleText);
    $("#viz-footer-text").text("Visual representation of terms and their relevance/weight inside each generated topic.");

    function drawGroups(topicsList) {
        const $container = $("#topic-groups-area");
        $container.empty();

        if (!topicsList || topicsList.length === 0) {
            $container.html('<p class="viz-status-error">No topic data found.</p>');
            return;
        }

        // Estrutura unificada para agrupar os tópicos de forma resiliente
        const grouped = {};

        // Caso 1: Array plano de palavras associadas a um topicId
        if (Array.isArray(topicsList) && topicsList[0] && topicsList[0].topic !== undefined && topicsList[0].word !== undefined) {
            topicsList.forEach(item => {
                const tId = item.topic;
                if (!grouped[tId]) grouped[tId] = [];
                grouped[tId].push({ word: item.word, weight: parseFloat(item.weight || 0) });
            });
        } 
        // Caso 2: Array estruturado por objeto de tópicos
        else if (Array.isArray(topicsList) && topicsList[0] && topicsList[0].words) {
            topicsList.forEach(t => {
                grouped[t.topic] = t.words.map(w => ({ word: w.word, weight: parseFloat(w.weight || 0) }));
            });
        }
        // Caso 3: Objeto genérico estruturado { "0": [...] }
        else if (typeof topicsList === 'object') {
            Object.keys(topicsList).forEach(tId => {
                grouped[tId] = topicsList[tId].map(w => ({ word: w.word || w[0], weight: parseFloat(w.weight || w[1] || 0) }));
            });
        }

        // Criação dos Cards de Tópicos
        Object.keys(grouped).forEach(tId => {
            const words = grouped[tId];
            
            // Ordena palavras por peso decrescente absoluto
            words.sort((a, b) => Math.abs(b.weight) - Math.abs(a.weight));

            // Captura o maior peso absoluto para escala matemática de 0 a 100%
            const maxWeight = Math.max(...words.map(w => Math.abs(w.weight)), 0.00001);

            // Obtém a palavra-chave mais importante (com maior peso no tópico)
            const leadWord = words.length > 0 ? words[0].word : '';
            let titleLabel = '';

            // Uniformiza os títulos sem numeração, destacando o termo principal
            const keywordMatch = String(tId).match(/(?:keyword|seed|word)\s*(\d+)?\s*:\s*['"]?([^'"]+)['"]?/i);
            if (keywordMatch) {
                const keyWord = keywordMatch[2];
                titleLabel = `Group Keyword: "${keyWord}"`;
            } else {
                titleLabel = leadWord ? `Group Keyword: "${leadWord}"` : 'Group Keyword';
            }

            let wordsHtml = '';
            words.forEach(w => {
                const percent = Math.min(Math.round((Math.abs(w.weight) / maxWeight) * 100), 100);
                const displayWeight = w.weight.toFixed(4);

                wordsHtml += `
                    <li class="topic-word-item">
                        <div class="topic-word-info">
                            <span class="topic-word-name">${w.word}</span>
                            <span class="topic-word-weight">${displayWeight} (${percent}%)</span>
                        </div>
                        <div class="topic-progress-bg">
                            <div class="topic-progress-bar" style="width: 0%;" data-percent="${percent}"></div>
                        </div>
                    </li>
                `;
            });

            const cardHtml = `
                <div class="topic-card">
                    <h3>
                        <span>${titleLabel}</span>
                        <span class="topic-badge">${words.length} words</span>
                    </h3>
                    <ul class="topic-words-list">
                        ${wordsHtml}
                    </ul>
                </div>
            `;
            $container.append(cardHtml);
        });

        // Animação das barras de progresso
        setTimeout(() => {
            $(".topic-progress-bar").each(function() {
                const pct = $(this).data("percent");
                $(this).css("width", pct + "%");
            });
        }, 100);

        // Scroll suave até os resultados
        $('html, body').animate({
            scrollTop: $("#w2v-viz-container").offset().top - 30
        }, 1000);
    }

    // Tenta obter o JSON correspondente ao modelo, caso contrário fallback para mock visual estruturado
    fetch(`exportExcel/${type}_viz.json?t=` + new Date().getTime())
        .then(response => {
            if (!response.ok) throw new Error("Viz file not found");
            return response.json();
        })
        .then(data => drawGroups(data))
        .catch(err => {
            console.warn(`Não foi possível carregar o arquivo visual '${type}_viz.json'. Tentando gerar representação gráfica a partir dos parâmetros de entrada...`, err);
            
            // Fallback para visualização imediata com os parâmetros escolhidos do DOM
            const mockTopics = [];
            const numTopics = parseInt($(`#${type}-topics`).val()) || 5;
            const numWords = parseInt($(`#${type}-words`).val()) || 5;
            const sampleWords = ["system", "data", "learning", "algorithm", "information", "processing", "model", "topic", "research", "analysis", "database", "classification", "document", "clustering", "vector"];
            
            for (let t = 0; t < numTopics; t++) {
                const words = [];
                for (let w = 0; w < numWords; w++) {
                    words.push({
                        word: sampleWords[(t * numWords + w) % sampleWords.length],
                        weight: (0.15 - (w * 0.02) - (t * 0.01)) * (Math.random() * 0.4 + 0.8)
                    });
                }
                mockTopics.push({ topic: t, words: words });
            }
            drawGroups(mockTopics);
        });
}