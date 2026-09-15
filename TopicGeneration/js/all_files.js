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
                <li class="list-item-card" style="cursor:pointer; border-bottom: 1px solid #eee; padding: 15px 10px; transition: background-color 0.2s;">
                    <div class="item-header">
                        <strong class="name">${item.title || 'Untitled'}</strong>
                        <span class="year">(${item.year || 'N/A'})</span>
                    </div>
                    <p class="abstract" style="display: none;">${item.abstract || 'No abstract available.'}</p>
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
                    // Feedback visual de seleção (igual ao antigo list_values)
                    document.querySelectorAll(".list-item-card").forEach(el => el.style.backgroundColor = "transparent");
                    card.style.backgroundColor = "#5279c04d";

                    const fullText = card.querySelector(".abstract").innerText;
                    const fullFileDiv = document.querySelector("#full-file");
                    const alertInfo = document.querySelector(".alert--info");

                    if (alertInfo) alertInfo.style.display = "none";
                    
                    // Garante que o container apareça e receba o texto
                    fullFileDiv.style.display = "flex";
                    fullFileDiv.classList.add("is-open");
                    fullFileDiv.querySelector("p").innerText = fullText;
                }
            });
        })
        .catch(err => {
            console.error("Failed to fetch files:", err);
            listContainer.innerHTML = `<p class="error">Connection error. Could not load data.</p>`;
        });
});