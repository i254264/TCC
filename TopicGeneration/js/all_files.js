document.addEventListener("DOMContentLoaded", function () {
    const listContainer = document.querySelector("#list-values");

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
                <div class="list-item-card" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                    <div class="item-header">
                        <span class="item-title"><strong>Title:</strong> ${item.title || 'Untitled'}</span>
                        <span class="item-year">(${item.year || 'N/A'})</span>
                    </div>
                    <div class="item-abstract" style="margin-top: 10px; color: #666;">
                        <p><strong>Abstract:</strong> ${item.abstract || 'No abstract available.'}</p>
                    </div>
                </div>
            `).join("");
        })
        .catch(err => {
            console.error("Failed to fetch files:", err);
            listContainer.innerHTML = `<p class="error">Connection error. Could not load data.</p>`;
        });
});