// scripts.js
document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const container = document.querySelector('.container');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        container.classList.toggle('sidebar-open');
    });
});
