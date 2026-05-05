@extends('layouts.desktop')

@section('title', 'Modifier mon profil')
@section('page-title', 'Modifier mon profil')
@section('page-subtitle', 'Mettez à jour vos informations personnelles')

@push('scripts')
    @vite('resources/js/auth.js')
@endpush

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    @if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4">
        <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if (session('success_password'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4">
        <p class="text-sm text-emerald-700 font-medium">{{ session('success_password') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('profil.update') }}">
        @csrf
        @method('PUT')

        @if ($errors->hasAny(['firstname', 'name', 'email']))
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-4">
            <ul class="space-y-1">
                @foreach (['firstname', 'name', 'email'] as $field)
                    @foreach ($errors->get($field) as $error)
                        <li class="text-sm text-red-600 font-medium">{{ $error }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
            <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-5">
                Informations du compte
            </p>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Prénom</label>
                    <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Adresse e-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('dashboard') }}"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors no-underline">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-[#222a60] hover:bg-[#1a2050] text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>

    {{-- ── Mot de passe ── --}}
    <form method="POST" action="{{ route('profil.update-password') }}" id="registerForm" novalidate>
        @csrf
        @method('PUT')

        @if ($errors->hasAny(['current_password', 'password']))
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-4">
            <ul class="space-y-1">
                @foreach (['current_password', 'password'] as $field)
                    @foreach ($errors->get($field) as $error)
                        <li class="text-sm text-red-600 font-medium">{{ $error }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
            <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-5">
                Modifier le mot de passe
            </p>

            {{-- Mot de passe actuel --}}
            <div class="mb-4">
                <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Mot de passe actuel
                </label>
                <div class="relative">
                    <input id="current_password" name="current_password" type="password"
                        autocomplete="current-password"
                        placeholder="Votre mot de passe actuel"
                        class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors
                               @error('current_password') border-red-300 bg-red-50 @enderror">
                    <button type="button" onclick="togglePassword('current_password', 'eye-current')"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg id="eye-current" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Nouveau mot de passe --}}
            <div class="mb-4">
                <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Nouveau mot de passe
                </label>
                <div class="relative">
                    <input id="password" name="password" type="password"
                        autocomplete="new-password"
                        placeholder="8 caractères minimum"
                        class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors">
                    <button type="button" onclick="togglePassword('password', 'eye-pwd')"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg id="eye-pwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                {{-- Barre de force — mêmes IDs que auth.js --}}
                <div class="mt-2">
                    <div class="strength-bar" id="strengthBar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <p class="text-[10px] mt-1 font-mono transition-colors duration-200" id="strengthLabel"
                        style="color: rgba(148,163,184,0.5);">
                        Saisissez un mot de passe
                    </p>
                </div>
            </div>

            {{-- Confirmation --}}
            <div>
                <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                    Confirmer le nouveau mot de passe
                </label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        autocomplete="new-password"
                        placeholder="Répétez le nouveau mot de passe"
                        class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 outline-none focus:border-[#222a60] focus:bg-white transition-colors">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg id="eye-confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                {{-- Même ID que auth.js --}}
                <p class="error-msg" id="confirmError">Les mots de passe ne correspondent pas.</p>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-[#222a60] hover:bg-[#1a2050] text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Changer le mot de passe
            </button>
        </div>
    </form>

</div>

@endsection
