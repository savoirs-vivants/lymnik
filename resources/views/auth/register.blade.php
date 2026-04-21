<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — Lymnik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-hero flex items-center justify-center min-h-screen px-4 py-12 font-grotesk">

    <div class="glass-card w-full max-w-[480px] rounded-2xl p-8 sm:p-10 shadow-2xl">
        <div class="gap-3 mb-8">
            <span class="font-grotesk font-bold text-white text-lg tracking-tight">Lymnik</span>
        </div>
        <div class="mb-7">
            <h1 class="font-grotesk text-2xl font-bold text-white mb-1">Créer un compte</h1>
            <p class="text-slate-400 text-sm">
                Déjà inscrit ?
                <a href="{{ route('login') }}"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-150 underline underline-offset-2">
                    Se connecter
                </a>
            </p>
        </div>
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl border border-red-400/20 bg-red-400/10">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400 text-xs flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
            @csrf
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="field-label block mb-1.5">Prénom</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}"
                            placeholder="Jean" autocomplete="given-name"
                            class="field w-full rounded-xl px-4 py-3 text-sm @error('first_name') error @enderror">
                        @error('first_name')
                            <p class="error-msg visible">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="field-label block mb-1.5">Nom</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}"
                            placeholder="Dupont" autocomplete="family-name"
                            class="field w-full rounded-xl px-4 py-3 text-sm @error('last_name') error @enderror">
                        @error('last_name')
                            <p class="error-msg visible">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="email" class="field-label block mb-1.5">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        placeholder="jean.dupont@exemple.fr" autocomplete="email"
                        class="field w-full rounded-xl px-4 py-3 text-sm @error('email') error @enderror">
                    @error('email')
                        <p class="error-msg visible">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="field-label block mb-1.5">Mot de passe</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="8 caractères minimum"
                            autocomplete="new-password"
                            class="field w-full rounded-xl px-4 py-3 pr-11 text-sm @error('password') error @enderror">
                        <button type="button" onclick="togglePassword('password', 'eye-pwd')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors duration-150">
                            <svg id="eye-pwd" class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="strength-bar" id="strengthBar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <p class="text-[10px] mt-1 font-mono transition-colors duration-200" id="strengthLabel"
                            style="color: rgba(148,163,184,0.5);">
                            Saisissez un mot de passe
                        </p>
                    </div>

                    @error('password')
                        <p class="error-msg visible">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="field-label block mb-1.5">Confirmer le mot de
                        passe</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            placeholder="Répétez votre mot de passe" autocomplete="new-password"
                            class="field w-full rounded-xl px-4 py-3 pr-11 text-sm">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors duration-150">
                            <svg id="eye-confirm" class="w-4.5 h-4.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="error-msg" id="confirmError">Les mots de passe ne correspondent pas.</p>
                </div>
                <div class="flex items-start gap-3 pt-1">
                    <input id="terms" name="terms" type="checkbox"
                        class="custom-checkbox mt-0.5 @error('terms') ring-2 ring-red-400/40 @enderror"
                        {{ old('terms') ? 'checked' : '' }}>
                    <label for="terms" class="text-slate-400 text-xs leading-relaxed cursor-pointer select-none">
                        J'accepte les
                        <a href="/conditions"
                            class="text-blue-400 hover:text-blue-300 underline underline-offset-2 transition-colors duration-150">
                            conditions d'utilisation
                        </a>
                        et la
                        <a href="/confidentialite"
                            class="text-blue-400 hover:text-blue-300 underline underline-offset-2 transition-colors duration-150">
                            politique de confidentialité
                        </a>.
                    </label>
                </div>
                <button type="submit"
                    class="btn-submit w-full rounded-xl py-3.5 text-white font-grotesk font-semibold text-sm flex items-center justify-center gap-2">
                    Créer mon compte
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>

            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            icon.innerHTML = show ?
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                            a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                            M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29
                            M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                            a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>` :
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }

        const pwdInput = document.getElementById('password');
        const fill = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        const bar = document.getElementById('strengthBar');

        const levels = [{
                min: 0,
                max: 0,
                pct: '0%',
                color: 'transparent',
                text: 'Saisissez un mot de passe',
                textColor: 'rgba(148,163,184,0.5)'
            },
            {
                min: 1,
                max: 2,
                pct: '25%',
                color: '#f87171',
                text: 'Très faible',
                textColor: '#f87171'
            },
            {
                min: 3,
                max: 4,
                pct: '50%',
                color: '#fb923c',
                text: 'Faible',
                textColor: '#fb923c'
            },
            {
                min: 5,
                max: 6,
                pct: '75%',
                color: '#facc15',
                text: 'Moyen',
                textColor: '#facc15'
            },
            {
                min: 7,
                max: 99,
                pct: '100%',
                color: '#16987c',
                text: 'Fort',
                textColor: '#16987c'
            },
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

        const confirmInput = document.getElementById('password_confirmation');
        const confirmError = document.getElementById('confirmError');

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
        document.getElementById('registerForm').addEventListener('submit', (e) => {
            checkMatch();
            if (pwdInput.value !== confirmInput.value) e.preventDefault();
        });
    </script>

</body>

</html>
