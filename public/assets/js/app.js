document.addEventListener('DOMContentLoaded', function () {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function (element) {
        new bootstrap.Tooltip(element);
    });

    const visitDateInput = document.querySelector('input[name="visit_date"]');
    if (visitDateInput) {
        const today = new Date().toISOString().split('T')[0];
        visitDateInput.setAttribute('min', today);
    }
});
