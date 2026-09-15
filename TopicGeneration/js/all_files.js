document.addEventListener("DOMContentLoaded", function () {
    const listContainer = document.querySelector("#all-files .list");

    if (!listContainer) return;

    // Busca os dados do banco via PHP
    fetch("php/get_files.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                listContainer.innerHTML = `<p class="error">Error: ${data.error}</p>`;
                return;
            }

            if (data.length === 0) {
                listContainer.innerHTML = `<p class="info">No records found in the database.</p>`;
                return;
            }

            // Mapeia os dados e substitui o conteúdo estático do container
            listContainer.innerHTML = data.map(item => `
                <li class="list-item-card" style="cursor:pointer; border-bottom: 1px solid #eee; padding: 15px 0;">
                    <div class="item-header">
                        <strong class="name">${item.title || 'Untitled'}</strong>
                        <span class="year">(${item.year || 'N/A'})</span>
                    </div>
                    <p class="abstract" style="margin-top: 10px; color: #666;">${item.abstract || 'No abstract available.'}</p>
                </li>
            `).join("");

            // Inicializa o List.js (habilita a busca e ordenação do seu HTML)
            new List('all-files', {
                valueNames: ['name', 'year', 'abstract'],
                page: 10,
                pagination: true
            });

            // Lógica para mostrar o texto completo ao clicar no item (conforme seu HTML)
            listContainer.addEventListener("click", function(e) {
                const card = e.target.closest(".list-item-card");
                if (card) {
                    const fullText = card.querySelector(".abstract").innerText;
                    document.querySelector("#full-file p").innerText = fullText;
                    const alertInfo = document.querySelector(".alert--info");
                    if (alertInfo) alertInfo.style.display = "none";
                }
            });
        })
        .catch(err => {
            console.error("Failed to fetch files:", err);
            listContainer.innerHTML = `<p class="error">Connection error. Could not load data.</p>`;
        });
});