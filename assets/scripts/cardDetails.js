const modal = document.getElementById("cardModal");
const modalBody = document.getElementById("modalBody");
const cardItems = document.querySelectorAll(".card-item");

// Get data from modal attributes
const cardDetailsUrl = modal.getAttribute("data-card-details-url");
const licenseSelected = modal.getAttribute("data-license");

// Icon menu selector
const burgerContainer = document.querySelector(".burger-container");
const profileContainer = document.querySelector(".profile-container");

// Open details and load data
cardItems.forEach(card => {
    card.addEventListener("click", function() {
        const cardId = this.getAttribute("data-card-id");
        loadCardDetails(cardId);
        modal.classList.add("active");
        document.body.classList.add("modal-open");
        hideMenuIcons();
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

// Hide menu icons
function hideMenuIcons() {
    if (burgerContainer) burgerContainer.style.display = "none";
    if (profileContainer) profileContainer.style.display = "none";
}

// Display menu icons
function showMenuIcons() {
    if (burgerContainer) burgerContainer.style.display = "";
    if (profileContainer) profileContainer.style.display = "";
}

// Close details with escape key
document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        modal.classList.remove("active");
        document.body.classList.remove("modal-open");
        showMenuIcons();
    }
});

// Close details
modal.addEventListener("click", function(event) {
    // Click on backdrop
    if (event.target === modal) {
        modal.classList.remove("active");
        document.body.classList.remove("modal-open");
        showMenuIcons();
    }
    // Click on cross
    if (event.target.classList.contains("modal-close-btn")) {
        modal.classList.remove("active");
        document.body.classList.remove("modal-open");
        showMenuIcons();
    }
});

// Accordion detail
document.addEventListener("click", function(event) {
    const header = event.target.closest(".accordion-header");
    if (header) {
        const item = header.parentElement;
        item.classList.toggle("active");
    }
});

// Disable turbo for specific route
document.addEventListener("turbo:before-visit", (event) => {
    if (event.detail.url.includes("/search")) {
        event.preventDefault();
        window.location.href = event.detail.url;
    }
});
