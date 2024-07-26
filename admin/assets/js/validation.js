// validation.js - Kliens oldali validáció a felhasználó hozzáadása űrlaphoz

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addUserForm');
    const inputs = form.querySelectorAll('input');

    const validationRules = {
        username: {
            pattern: /^[a-z0-9_][a-z0-9_-]{2,30}$/i,
            message: 'A felhasználónév 3-31 karakter hosszú lehet, betűket, számokat, alulvonást és kötőjelet tartalmazhat.'
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Kérjük, adjon meg egy érvényes e-mail címet.'
        },
        password: {
            pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
            message: 'A jelszónak legalább 8 karakter hosszúnak kell lennie, és tartalmaznia kell nagybetűt, kisbetűt, számot és speciális karaktert.'
        },
        default_domain: {
            pattern: /^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/,
            message: 'Kérjük, adjon meg egy érvényes domain nevet.'
        }
    };

    inputs.forEach(input => {
        const validationMessage = document.createElement('div');
        validationMessage.className = 'validation-message';
        input.parentNode.insertBefore(validationMessage, input.nextSibling);

        input.addEventListener('input', function () {
            validateInput(input);
        });
    });

    function validateInput(input) {
        const rule = validationRules[input.id];
        const validationIcon = input.parentNode.querySelector('.validation-icon');
        const validationMessage = input.parentNode.querySelector('.validation-message');

        if (rule && input.value) {
            if (rule.pattern.test(input.value)) {
                validationIcon.textContent = '✔';
                validationIcon.style.color = 'green';
                validationMessage.textContent = '';
            } else {
                validationIcon.textContent = '❌';
                validationIcon.style.color = 'red';
                validationMessage.textContent = rule.message;
            }
        } else {
            validationIcon.textContent = '';
            validationMessage.textContent = '';
        }
    }

    form.addEventListener('submit', function (event) {
        let isValid = true;
        inputs.forEach(input => {
            if (validationRules[input.id] && !validationRules[input.id].pattern.test(input.value)) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Kérjük, javítsa a hibákat az űrlapon!');
        }
    });
});