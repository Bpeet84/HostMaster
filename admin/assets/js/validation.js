// validation.js - Form Validáció - HostMaster

document.addEventListener('DOMContentLoaded', function () {
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const defaultDomainInput = document.getElementById('default_domain');

    usernameInput.addEventListener('input', validateUsername);
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);
    defaultDomainInput.addEventListener('input', validateDefaultDomain);

    function validateUsername() {
        const username = usernameInput.value;
        const regex = /^[a-z0-9_][a-z0-9_-]{2,30}$/i;
        updateValidationIcon(username, regex, 'username-validation');
    }

    function validateEmail() {
        const email = emailInput.value;
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        updateValidationIcon(email, regex, 'email-validation');
    }

    function validatePassword() {
        const password = passwordInput.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        updateValidationIcon(password, regex, 'password-validation');
    }

    function validateDefaultDomain() {
        const domain = defaultDomainInput.value;
        const regex = /^(?!:\/\/)([a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,}$/;
        updateValidationIcon(domain, regex, 'default_domain-validation');
    }

    function updateValidationIcon(value, regex, iconId) {
        const icon = document.getElementById(iconId);
        if (regex.test(value)) {
            icon.textContent = '✔️';
            icon.style.color = 'green';
        } else {
            icon.textContent = '❌';
            icon.style.color = 'red';
        }
    }
});
