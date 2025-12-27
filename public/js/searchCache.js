    // Save selection in localStorage
    function saveSearchCache() {
    const licenseSelect = document.querySelector("[name='license']");
    const setSelect = document.querySelector("[name='choice']");

    if (licenseSelect?.value) {
    localStorage.setItem("search_license", licenseSelect.value);
}
    if (setSelect?.value) {
    localStorage.setItem("search_set", setSelect.value);
}
}

    // Get selection from localStorage
    function restoreSearchCache() {
    const licenseSelect = document.querySelector("[name='license']");
    const setSelect = document.querySelector("[name='choice']");

    const cachedLicense = localStorage.getItem("search_license");
    const cachedSet = localStorage.getItem("search_set");

    if (licenseSelect && cachedLicense) {
    licenseSelect.value = cachedLicense;
}
    if (setSelect && cachedSet) {
    setSelect.value = cachedSet;
}
}

    // Listen form changement
    document.querySelectorAll("[name='license'], [name='choice']").forEach(input => {
    input.addEventListener("change", saveSearchCache);
});

    // Restore when loading
    document.addEventListener("DOMContentLoaded", restoreSearchCache);
