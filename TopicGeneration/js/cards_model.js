window.addEventListener("DOMContentLoaded", function(){
    literalString();
});

function literalString(){
    const models = [
        {
            titulo: 'LSA',
            texto: 'Latent Semantic Analysis is a natural language processing method that uncovers relationships between terms and documents. By using Singular Value Decomposition (SVD), it simplifies complex document matrices to identify latent concepts and semantic patterns.',
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
            texto: 'Latent Dirichlet Allocation is a generative statistical model that allows sets of observations to be explained by unobserved groups. In text modeling, it assumes each document is a mixture of topics, and each topic is a mixture of words, enabling unsupervised topic discovery.',
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
            texto: 'Word2Vec is a group of related models used to produce word embeddings. These shallow, two-layer neural networks are trained to reconstruct linguistic contexts of words, mapping them into a multi-dimensional vector space where semantically similar words are placed close to each other.',
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
}
