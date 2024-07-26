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

    if (switchUserBtn && userSelect) {
        switchUserBtn.addEventListener('click', () => {
            const selectedUserId = userSelect.value;
            if (selectedUserId) {
                const csrfToken = switchUserBtn.getAttribute('data-csrf-token');

                // AJAX kérés a felhasználóváltáshoz
                fetch('switch_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'user_id=' + encodeURIComponent(selectedUserId) + '&csrf_token=' + encodeURIComponent(csrfToken)
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            throw new Error(data.error || 'Ismeretlen hiba történt.');
                        }
                    })
                    .catch(error => {
                        console.error('Hiba:', error);
                        let errorMessage = 'Hiba történt a felhasználóváltás során.';
                        if (error.error) {
                            errorMessage += ' ' + error.error;
                        } else if (error.message) {
                            errorMessage += ' ' + error.message;
                        }
                        alert(errorMessage);
                    });
            } else {
                alert('Kérjük, válasszon egy felhasználót.');
            }
        });
    }
});