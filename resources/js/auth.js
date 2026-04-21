// resources/js/auth.js

// 1. Fonction globale pour afficher/masquer le mot de passe
// On l'attache à "window" pour qu'elle puisse être appelée depuis tes "onclick" en HTML
window.togglePassword = function(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (!input || !icon) return;

    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';

    icon.innerHTML = show ?
        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7 a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243 M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29 M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7 a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>` :
        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7 -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
};

// 2. Logique exécutée une fois la page chargée
document.addEventListener('DOMContentLoaded', () => {

    const pwdInput = document.getElementById('password');
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    const bar = document.getElementById('strengthBar');
    const confirmInput = document.getElementById('password_confirmation');
    const confirmError = document.getElementById('confirmError');
    const registerForm = document.getElementById('registerForm');

    // --- A. Barre de force du mot de passe (Uniquement sur Register) ---
    if (pwdInput && fill && label && bar) {
        const levels = [
            { min: 0, max: 0, pct: '0%', color: 'transparent', text: 'Saisissez un mot de passe', textColor: 'rgba(148,163,184,0.5)' },
            { min: 1, max: 2, pct: '25%', color: '#f87171', text: 'Très faible', textColor: '#f87171' },
            { min: 3, max: 4, pct: '50%', color: '#fb923c', text: 'Faible', textColor: '#fb923c' },
            { min: 5, max: 6, pct: '75%', color: '#facc15', text: 'Moyen', textColor: '#facc15' },
            { min: 7, max: 99, pct: '100%', color: '#16987c', text: 'Fort', textColor: '#16987c' },
        ];

        function getScore(pwd) {
            let s = 0;
            if (pwd.length >= 8) s += 2;
            if (pwd.length >= 12) s += 1;
            if (/[A-Z]/.test(pwd)) s += 1;
            if (/[a-z]/.test(pwd)) s += 1;
            if (/[0-9]/.test(pwd)) s += 1;
            if (/[^A-Za-z0-9]/.test(pwd)) s += 2;
            return pwd.length === 0 ? 0 : Math.min(s, 8);
        }

        pwdInput.addEventListener('input', () => {
            const score = getScore(pwdInput.value);
            const lvl = levels.find(l => score >= l.min && score <= l.max) || levels[0];
            fill.style.width = lvl.pct;
            fill.style.backgroundColor = lvl.color;
            label.textContent = lvl.text;
            label.style.color = lvl.textColor;
            bar.style.opacity = pwdInput.value.length ? '1' : '0.6';
        });
    }

    // --- B. Vérification de la confirmation du mot de passe (Uniquement sur Register) ---
    if (pwdInput && confirmInput && confirmError && registerForm) {
        function checkMatch() {
            if (confirmInput.value.length === 0) {
                confirmError.classList.remove('visible');
                confirmInput.classList.remove('error');
                return;
            }
            const mismatch = pwdInput.value !== confirmInput.value;
            confirmError.classList.toggle('visible', mismatch);
            confirmInput.classList.toggle('error', mismatch);
        }

        confirmInput.addEventListener('input', checkMatch);

        pwdInput.addEventListener('input', () => {
            if (confirmInput.value) checkMatch();
        });

        registerForm.addEventListener('submit', (e) => {
            checkMatch();
            if (pwdInput.value !== confirmInput.value) {
                e.preventDefault(); // Empêche l'envoi si ça ne correspond pas
            }
        });
    }
});
