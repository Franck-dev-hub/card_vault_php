const modal = document.getElementById("cardModal");
const modalBody = document.getElementById("modalBody");
const cardItems = document.querySelectorAll(".card-item");

// Get the selected license and set
const licenseSelected = "{{ licenseSelected }}";
const setSelected = "{{ setSelected }}";

// Open the modal and load data when a card is clicked
cardItems.forEach(card => {
    card.addEventListener("click", function() {
        const cardId = this.getAttribute("data-card-id");
        loadCardDetails(cardId);
        modal.classList.add("active");
    });
});

// Load card details via AJAX
function loadCardDetails(cardId) {
    // Show loading spinner
    modalBody.innerHTML = '<div class="modal-loading"><div class="spinner"></div></div>';

    const url = new URL("{{ path('card_details', {'id': 'CARD_ID'}) }}".replace("CARD_ID", cardId), window.location.origin);
    url.searchParams.append("license", licenseSelected);
    url.searchParams.append("set", setSelected);

    fetch(url.toString(), {
        method: "GET",
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error loading the card");
            }
            return response.json();
        })
        .then(data => {
            displayCardDetails(data);
        })
        .catch(error => {
            console.error("Error:", error);
            modalBody.innerHTML = '<div class="modal-error">Error loading the card</div>';
        });
}

// Display card details in the modal
function displayCardDetails(card) {
    modalBody.innerHTML = `
        <img class="modal-card-image" src="${card.image}" alt="${card.name}">
        <div class="modal-card-details">
            <h2>${card.name}</h2>
            <p><strong>ID:</strong> ${card.id}</p>
            <p><strong>Set:</strong> ${card.set || "N/A"}</p>
            ${card.rarity ? `<p><strong>Rarity:</strong> ${card.rarity}</p>` : ""}
            ${card.description ? `<p><strong>Description:</strong> ${card.description}</p>` : ""}
            ${card.price ? `<p><strong>Price:</strong> ${card.price}€</p>` : ""}
        </div>
    `;
}

// Close the modal when clicking on the black background
modal.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.classList.remove("active");
    }
});

// Close the modal with the Escape key
document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        modal.classList.remove("active");
    }
});
