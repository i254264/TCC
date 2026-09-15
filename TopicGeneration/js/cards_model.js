window.addEventListener("DOMContentLoaded", function(){
    literalString();
});

function literalString(){
    const cards = [
        {
            'titulo': 'LDA',
            'texto': "Latent Dirichlet Allocation (LDA) is a statistical model for uncovering hidden topics within a collection of documents. It's ideal for analyzing large text datasets where topics are not explicitly labeled, helping to organize and understand thematic structures in articles, blogs, or research papers."
        },
        {
            'titulo': 'Word2Vec',
            'texto': "Latent Dirichlet Allocation (LDA) is a statistical model for uncovering hidden topics within a collection of documents. It's ideal for analyzing large text datasets where topics are not explicitly labeled, helping to organize and understand thematic structures in articles, blogs, or research papers."
        },
        {
            'titulo': 'pLSA',
            'texto': "Latent Dirichlet Allocation (LDA) is a statistical model for uncovering hidden topics within a collection of documents. It's ideal for analyzing large text datasets where topics are not explicitly labeled, helping to organize and understand thematic structures in articles, blogs, or research papers."
        },
        {
            'titulo': 'LSA',
            'texto': "Latent Dirichlet Allocation (LDA) is a statistical model for uncovering hidden topics within a collection of documents. It's ideal for analyzing large text datasets where topics are not explicitly labeled, helping to organize and understand thematic structures in articles, blogs, or research papers."
        }
    ];

    function cardsHTML(model){
        return `
        <div class="card">
            <div class="tit-card">
                <img src="img/hashtag.png" alt="${model.titulo}">
                <h2 card-title>${model.titulo}</p>
            </div>
            <p class="text-card">${model.texto}</p>
            <a class="btn-card" href="#" role="button">Read More</a>
        </div>
        `;
    }

    document.getElementById('card-models-body').innerHTML+=`${cards.map((card) => cardsHTML(card)).join('')}`;
}
