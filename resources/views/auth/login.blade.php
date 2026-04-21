<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter — Lymnik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])
</head>

<body class="bg-hero flex items-center justify-center min-h-screen px-4 py-12 font-grotesk">

    <div class="glass-card w-full max-w-[440px] rounded-2xl p-8 sm:p-10 shadow-2xl">
        <div class="gap-3 mb-8">
            <span class="font-grotesk font-bold text-white text-lg tracking-tight">Lymnik</span>
        </div>
        <div class="mb-7">
            <h1 class="font-grotesk text-2xl font-bold text-white mb-1">Se connecter</h1>
            <p class="text-slate-400 text-sm">
                Pas encore de compte ?
                <a href="{{ route('register') }}"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-150 underline underline-offset-2">
                    Créer un compte
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
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl border border-sv-green/25 bg-sv-green/10">
                <p class="text-sv-green text-xs flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('status') }}
                </p>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="email" class="field-label block mb-1.5">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        placeholder="jean.dupont@exemple.fr" autocomplete="email" autofocus
                        class="field w-full rounded-xl px-4 py-3 text-sm @error('email') error @enderror">
                    @error('email')
                        <p class="text-red-400 text-[11px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="field-label">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-blue-400 hover:text-blue-300 text-[11px] font-mono transition-colors duration-150">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="••••••••"
                            autocomplete="current-password"
                            class="field w-full rounded-xl px-4 py-3 pr-11 text-sm @error('password') error @enderror">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors duration-150"
                            aria-label="Afficher le mot de passe">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-[11px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input id="remember" name="remember" type="checkbox" class="custom-checkbox"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-slate-400 text-xs cursor-pointer select-none">
                        Se souvenir de moi
                    </label>
                </div>

                <button type="submit"
                    class="btn-submit w-full rounded-xl py-3.5 text-white font-grotesk font-semibold text-sm flex items-center justify-center gap-2">
                    Se connecter
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>

            </div>
        </form>

        <div class="divider my-6">OU</div>

        <p class="text-center text-slate-400 text-xs">
            Vous avez un code classe ?
            <a href="/"
                class="text-blue-400 hover:text-blue-300 underline underline-offset-2 transition-colors duration-150">
                Rejoindre avec un code
            </a>
        </p>

    </div>
</body>

</html>
