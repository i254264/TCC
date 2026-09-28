document.addEventListener("DOMContentLoaded", function () {
    const helpBtn = document.getElementById('help_button');
    const popup = document.getElementById('popUP');
    const overlay = document.getElementById('popup-overlay');
    const closeBtn = document.getElementById('close_popup');
    const tabs = document.querySelectorAll('.tabs .tab');
    const tabContents = document.querySelectorAll('#popUP .tab-content');

    // Garante que o popup e o overlay iniciem ocultos se ainda não tiverem a classe
    if (popup && !popup.classList.contains('hidden')) popup.classList.add('hidden');
    if (overlay && !overlay.classList.contains('hidden')) overlay.classList.add('hidden');

    function toggleGuide(show) {
        if (!popup || !overlay) return;
        popup.classList.toggle('hidden', !show);
        overlay.classList.toggle('hidden', !show);
    }

    if (helpBtn) helpBtn.addEventListener('click', () => toggleGuide(true));
    if (closeBtn) closeBtn.addEventListener('click', () => toggleGuide(false));
    if (overlay) overlay.addEventListener('click', () => toggleGuide(false));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') toggleGuide(false);
    });

    // Alternância entre as abas 'About' e 'Upload Rules'
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            tabs.forEach(t => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            tabContents.forEach(content => {
                if (content.id === `tab-${target}`) {
                    content.classList.add('is-active');
                } else {
                    content.classList.remove('is-active');
                }
            });
        });
    });
});