<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — Lymnik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-hero flex items-center justify-center min-h-screen px-4 py-12 font-grotesk">

    <div class="glass-card w-full max-w-[440px] rounded-2xl p-8 sm:p-10 shadow-2xl">

        <div class="mb-8">
            <span class="font-grotesk font-bold text-white text-lg tracking-tight">Lymnik</span>
        </div>

        <div class="mb-7">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
            </div>
            <h1 class="font-grotesk text-2xl font-bold text-white mb-1">Mot de passe oublié</h1>
            <p class="text-slate-400 text-sm">
                Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl border border-sv-green/25 bg-sv-green/10">
                <p class="text-sv-green text-xs flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('status') }}
                </p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl border border-red-400/20 bg-red-400/10">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400 text-xs flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

                <button type="submit"
                    class="btn-submit w-full rounded-xl py-3.5 text-white font-grotesk font-semibold text-sm flex items-center justify-center gap-2">
                    Envoyer le lien
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 text-[11px] font-mono transition-colors duration-150 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la connexion
            </a>
        </div>

    </div>
</body>

</html>
