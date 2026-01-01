export function cardLanguagePopUp(select) {
    const selectedLang = select.value;
    const message = select.dataset.warningMessage;

    if (selectedLang !== "en") {
        const confirmed = confirm(message);

        if (confirmed) {
            select.form.submit();
        } else {
            select.value = "en";
        }
    } else {
        select.form.submit();
    }
}

window.cardLanguagePopUp = cardLanguagePopUp;
