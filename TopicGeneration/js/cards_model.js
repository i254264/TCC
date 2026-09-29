window.addEventListener("DOMContentLoaded", function(){
    literalString();
});

function literalString(){
    const models = [
        {
            titulo: 'LSA',
            link: 'https://radimrehurek.com/gensim/models/lsimodel.html',
            texto: 'Implemented via Gensim\'s LsiModel, Latent Semantic Analysis uncovers hidden semantic relationships between terms and documents through Singular Value Decomposition (SVD) on vector matrices.',
            chartHtml: `
                <div class="ring-chart" data-progress="80">
                    <div class="circle">
                        <div class="mask full">
                            <div class="fill bg"></div>
                        </div>
                        <div class="mask half">
                            <div class="fill bg"></div>
                            <div class="fill bg fix"></div>
                        </div>
                    </div>
                    <div class="ring-fill bg"></div>
                    <div class="small ring-chart" data-progress="60">
                        <div class="circle">
                            <div class="mask full">
                                <div class="fill bg"></div>
                            </div>
                            <div class="mask half">
                                <div class="fill bg"></div>
                                <div class="fill bg fix"></div>
                            </div>
                        </div>
                        <div class="ring-fill bg"></div>
                    </div>
                </div>
            `
        },
        {
            titulo: 'LDA',
            link: 'https://radimrehurek.com/gensim/models/ldamodel.html',
            texto: 'Implemented via Gensim\'s LdaModel, Latent Dirichlet Allocation is an unsupervised generative probabilistic model that assumes each document is a mixture of topics and each topic is a mixture of words.',
            chartHtml: `
                <div class="column-chart">
                    <div class="column" data-progress="60"></div>
                    <div class="column" data-progress="80"></div>
                    <div class="column" data-progress="50"></div>
                    <div class="column" data-progress="90"></div>
                </div>
            `
        },
        {
            titulo: 'W2V',
            link: 'https://radimrehurek.com/gensim/models/word2vec.html',
            texto: 'Implemented via Gensim\'s Word2Vec model, this neural architecture learns dense word embeddings from context windows, placing semantically related words close together in vector space.',
            chartHtml: `
                <div class="bar-chart">
                    <div class="bar" data-progress="70"></div>
                    <div class="bar" data-progress="90"></div>
                    <div class="bar" data-progress="50"></div>
                    <div class="bar" data-progress="80"></div>
                </div>
            `
        }
    ];

    const container = document.querySelector('#modelsCards .container');
    if (!container) return;

    container.innerHTML = models.map((model, index) => {
        const activeClass = index === 0 ? 'active' : '';
        return `
            <div class="panel ${activeClass}">
                <h3 class="panel-title">${model.titulo}</h3>
                <div class="panel-content">
                    <div class="panel-text">
                        <h2>${model.titulo}</h2>
                        <p>${model.texto}</p>
                    </div>
                    <div class="panel-chart">
                        ${model.chartHtml}
                    </div>
                    <a class="btn-card" href="${model.link}" target="_blank" rel="noopener noreferrer" role="button">Read More</a>
                </div>
            </div>
        `;
    }).join('');

    // Controladores de estado ativo diretamente sobre os elementos dinâmicos
    const panels = container.querySelectorAll('.panel');
    panels.forEach(panel => {
        panel.addEventListener('click', () => {
            panels.forEach(p => p.classList.remove('active'));
            panel.classList.add('active');
        });
    });

    // Evita conflito do clique no botão com a seleção do card pai
    const readMoreBtns = container.querySelectorAll('.btn-card');
    readMoreBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });
}
