// Admin általános JavaScript funkciók - HostMaster

document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');

    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    const switchUserBtn = document.getElementById('switch-user-btn');
    const userSelect = document.getElementById('user-select');

    switchUserBtn.addEventListener('click', () => {
        const selectedUserId = userSelect.value;
        if (selectedUserId) {
            window.location.href = `switch_user.php?user_id=${selectedUserId}`;
        } else {
            alert('Kérjük, válasszon egy felhasználót.');
        }
    });
});
