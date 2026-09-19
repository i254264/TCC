window.addEventListener("DOMContentLoaded", function(){
    let panel = document.getElementById('tutorialModel');
    let panelCards = document.getElementById('modelsCards');
    let lineAnimated = document.getElementById('line-path-vert');
    
    let btnTutorial = document.getElementById('btnTutorial');
    
    if (btnTutorial) {
        btnTutorial.onclick = () => {
            if (panel) panel.style.transform = `translateY(0)`;

            // Reseta e inicia a animação da linha decorativa
            if (lineAnimated) {
                lineAnimated.classList.remove('animate-path-vert');
                // Força um reflow para o navegador perceber a remoção da classe
                void lineAnimated.offsetWidth; 
                setTimeout(() => {
                    lineAnimated.classList.add('animate-path-vert');
                }, 800);
            }
        }
    }

    let btnCloseTutorial = document.getElementById('btnCloseTutorial');
    if (btnCloseTutorial && panelCards) {
        btnCloseTutorial.onclick = () => {
            panelCards.style.transform = `translateY(100%)`;
        }
    }
});