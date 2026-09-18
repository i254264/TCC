window.addEventListener("DOMContentLoaded", function(){
    let panel = document.getElementById('tutorialModel');
    let panelCards = document.getElementById('cards-models');
    let welcomeTit = document.getElementById('tit-welcome');
    
    let btnTutorial = document.getElementById('btnTutorial');
    
    btnTutorial.onclick = (e) => {
        e.preventDefault();
        // Faz o título de boas-vindas sumir para não ficar "por cima"
        welcomeTit.style.opacity = '0';
        welcomeTit.style.transform = 'translateY(-50px)';
        panel.style.transform = `translateY(0)`;
    }

    // Lógica para os cards expansíveis
    const cardsTutorial = document.querySelectorAll('.panel');

    cardsTutorial.forEach(card => {
        card.addEventListener('click', () => {
            if (card.classList.contains('active')) {
                // Se clicar no card que já está aberto, ele abre a próxima seção
                panelCards.style.transform = `translateY(0)`;
            } else {
                removeActiveClasses();
                card.classList.add('active');
            }
        });
    });

    function removeActiveClasses() {
        cardsTutorial.forEach(card => {
            card.classList.remove('active');
        });
    }

    let btnCloseTutorial = document.getElementById('btnCloseTutorial');
    btnCloseTutorial.onclick = () => {
        panelCards.style.transform = `translateY(100%)`;
    }
});