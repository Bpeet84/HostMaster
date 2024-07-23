/* scripts.js */

// Oldalsáv megjelenítése/elrejtése
document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const container = document.querySelector('.container');

    menuBtn.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        container.classList.toggle('sidebar-open');
    });
});
