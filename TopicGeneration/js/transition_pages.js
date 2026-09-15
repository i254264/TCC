window.addEventListener("DOMContentLoaded", function(){
    let panel = document.getElementById('tutorialModel');
    let panelCards = document.getElementById('cards-models');
    let lineAnimated = document.getElementById('line-path-vert');
    
    let transitionToCards = document.getElementById('lineToCards');
    let btnTutorial = document.getElementById('btnTutorial');
    
    transitionToCards.onclick = () => {
        panelCards.style.transform = `translateY(0)`;
    };
    btnTutorial.onclick = () => {
        panel.style.transform = `translateY(0)`;

        //adiciona a classe que vai animar a linha
        setTimeout(() => {
            lineAnimated.classList.add('animate-path-vert');
        }, 1000);
    }

    let btnCloseTutorial = document.getElementById('btnCloseTutorial');
    btnCloseTutorial.onclick = () => {
        panelCards.style.transform = `translateY(100%)`;
    }
});