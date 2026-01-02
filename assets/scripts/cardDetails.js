const modal = document.getElementById("cardModal");
const modalBody = document.getElementById("modalBody");
const cardItems = document.querySelectorAll(".card-item");

// Get data from modal attributes
const cardDetailsUrl = modal.getAttribute("data-card-details-url");
const licenseSelected = modal.getAttribute("data-license");

// Open details and load data
cardItems.forEach(card => {
    card.addEventListener("click", function() {
        const cardId = this.getAttribute("data-card-id");
        loadCardDetails(cardId);
        modal.classList.add("active");
        document.body.classList.add("modal-open");
    });
});

// Load card details via AJAX
function loadCardDetails(cardId) {
    // Show loading spinner
    modalBody.innerHTML = '<div class="modal-loading"><div class="spinner"></div></div>';

    // Build the URL with the card ID and license parameter
    const url = new URL(cardDetailsUrl.replace("PLACEHOLDER", cardId), window.location.origin);
    url.searchParams.append("license", licenseSelected);

    fetch(url.toString())
        .then(response => {
            if (!response.ok) {
                throw new Error("Error loading the card");
            }
            return response.text();
        })
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error("Error:", error);
            modalBody.innerHTML = '<div class="modal-error">Error loading the card</div>';
        });
}

// Close details with escape key
document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        modal.classList.remove("active");
        document.body.classList.remove("modal-open");
    }
});

// Close details by clicking outside the box
modal.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.classList.remove("active");
        document.body.classList.remove("modal-open");
    }
});
