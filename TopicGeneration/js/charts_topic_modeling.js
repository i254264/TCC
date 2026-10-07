/**
 * ==========================================================================
 * Lógica de Geração e Visualização de Gráficos e Tópicos (LSA, LDA, W2V)
 * ==========================================================================
 */

/**
 * Processa e desenha os cards de tópicos dinâmicos para LDA, LSA e Word2Vec.
 */
function renderTopicGroups(type, responseData) {
    $("#w2v-viz-container").fadeIn(800);
    $("#w2v-chart-area").hide();
    $("#topic-groups-area").show().html('<p class="viz-status-loading">Loading topic groups...</p>');
    
    // Atualiza cabeçalhos dinamicamente
    const titleText = type.toLowerCase() === 'w2v'
        ? "W2V Word Similarity & Keyword Weights"
        : type.toUpperCase() + " Topic Groups & Keyword Weights";
    $("#viz-title").text(titleText);
    
    const footerDescriptions = {
        'lda': "Topic Probability: values represent conditional probability P(w|z). Bars indicate relative proportion to the top topic term.",
        'lsa': "Strength Score: values represent latent projections (with sign). Bars indicate relative magnitude (|w|) to the highest loading term.",
        'w2v': "Context Similarity: values represent cosine similarity to the card keyword. Bars show positive similarity relative to the highest score displayed; non-positive values receive zero bar."
    };
    $("#viz-footer-text").text(footerDescriptions[type.toLowerCase()] || "Visualization of terms and their respective weights.");

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
            
            const isW2V = type.toLowerCase() === 'w2v';
            const isLSA = type.toLowerCase() === 'lsa';

            // Ordenação adequada a cada método:
            // LSA: magnitude absoluta |w| (força no componente)
            // Word2Vec e LDA: valor real decrescente (maior similaridade ou maior probabilidade primeiro)
            if (isLSA) {
                words.sort((a, b) => Math.abs(b.weight) - Math.abs(a.weight));
            } else {
                words.sort((a, b) => b.weight - a.weight);
            }

            // Referência de escala máxima para a barra de 100%
            let maxRef = 0.00001;
            if (isLSA) {
                maxRef = Math.max(...words.map(w => Math.abs(w.weight)), 0.00001);
            } else if (isW2V) {
                maxRef = Math.max(...words.map(w => w.weight), 0.00001);
            } else {
                maxRef = Math.max(...words.map(w => w.weight), 0.00001);
            }

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
                let percent = 0;
                if (isLSA) {
                    // LSA: magnitude relativa ao termo de maior carga
                    percent = Math.min(Math.round((Math.abs(w.weight) / maxRef) * 100), 100);
                } else if (isW2V) {
                    // Word2Vec: se cosseno for negativo, a barra não pontua positivamente
                    percent = w.weight > 0 ? Math.min(Math.round((w.weight / maxRef) * 100), 100) : 0;
                } else {
                    // LDA: proporção relativa em relação à maior probabilidade
                    percent = Math.min(Math.round((w.weight / maxRef) * 100), 100);
                }

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

    // Carrega o arquivo JSON correspondente ao modelo gerado
    fetch(`exportExcel/${type}_viz.json?t=` + new Date().getTime())
        .then(response => {
            if (!response.ok) throw new Error("Viz file not found");
            return response.json();
        })
        .then(data => drawGroups(data))
        .catch(err => {
            console.error(`Erro ao carregar o arquivo visual '${type}_viz.json':`, err);
            $("#topic-groups-area").html('<p class="viz-status-error">Could not load topic visualization data. Please verify if the model was generated successfully.</p>');
        });
}