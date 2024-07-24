document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    const switchUserBtn = document.getElementById('switch-user-btn');
    const userSelect = document.getElementById('user-select');
    const userSearch = document.getElementById('user-search');

    userSearch.addEventListener('input', () => {
        const query = userSearch.value.toLowerCase();
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `search_users.php?query=${query}`, true);
        xhr.onload = function () {
            if (this.status === 200) {
                const users = JSON.parse(this.responseText);
                userSelect.innerHTML = '<option value="">Válassz felhasználót</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.username;
                    userSelect.appendChild(option);
                });
            }
        }
        xhr.send();
    });

    switchUserBtn.addEventListener('click', () => {
        const selectedUserId = userSelect.value;
        if (selectedUserId) {
            window.location.href = `switch_user.php?user_id=${selectedUserId}`;
        } else {
            alert('Kérjük, válasszon egy felhasználót.');
        }
    });
});
