<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe — Lymnik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])
</head>

<body class="bg-hero flex items-center justify-center min-h-screen px-4 py-12 font-grotesk">

    <div class="glass-card w-full max-w-[440px] rounded-2xl p-8 sm:p-10 shadow-2xl">

        <div class="mb-8">
            <span class="font-grotesk font-bold text-white text-lg tracking-tight">Lymnik</span>
        </div>

        <div class="mb-7">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h1 class="font-grotesk text-2xl font-bold text-white mb-1">Nouveau mot de passe</h1>
            <p class="text-slate-400 text-sm">Choisissez un mot de passe fort pour sécuriser votre compte.</p>
        </div>

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

        <form method="POST" action="{{ route('password.update') }}" id="registerForm">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-5">

                <div>
                    <label for="email" class="field-label block mb-1.5">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email) }}"
                        autocomplete="email"
                        class="field w-full rounded-xl px-4 py-3 text-sm @error('email') error @enderror">
                    @error('email')
                        <p class="text-red-400 text-[11px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="field-label block mb-1.5">Nouveau mot de passe</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="••••••••"
                            autocomplete="new-password"
                            class="field w-full rounded-xl px-4 py-3 pr-11 text-sm @error('password') error @enderror">
                        <button type="button" onclick="togglePassword('password', 'eye-pwd')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors duration-150">
                            <svg id="eye-pwd" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-[11px] mt-1.5">{{ $message }}</p>
                    @enderror

                    {{-- Barre de force du mot de passe (même système que register) --}}
                    <div id="strengthBar" class="mt-3 space-y-1.5" style="opacity:0.6">
                        <div class="h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                            <div id="strengthFill" class="h-full rounded-full transition-all duration-300" style="width:0%;background:transparent"></div>
                        </div>
                        <p id="strengthLabel" class="text-[11px] font-mono" style="color:rgba(148,163,184,0.5)">Saisissez un mot de passe</p>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="field-label block mb-1.5">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••"
                            autocomplete="new-password"
                            class="field w-full rounded-xl px-4 py-3 pr-11 text-sm">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors duration-150">
                            <svg id="eye-confirm" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="confirmError" class="text-red-400 text-[11px] mt-1.5 invisible">Les mots de passe ne correspondent pas.</p>
                </div>

                <button type="submit"
                    class="btn-submit w-full rounded-xl py-3.5 text-white font-grotesk font-semibold text-sm flex items-center justify-center gap-2">
                    Réinitialiser le mot de passe
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

            </div>
        </form>

    </div>
</body>

</html>
