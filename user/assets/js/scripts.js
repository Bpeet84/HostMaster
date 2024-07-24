document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const headerNav = document.querySelector('header nav');

    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            headerNav.classList.toggle('active');
        });
    }
});
