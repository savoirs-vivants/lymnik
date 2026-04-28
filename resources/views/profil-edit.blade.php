@extends('layouts.desktop')

@section('title', 'Modifier mon profil')
@section('page-title', 'Modifier mon profil')
@section('page-subtitle', 'Mettez à jour vos informations personnelles')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-sm text-red-600 font-medium">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4">
        <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('profil.update') }}">
        @csrf
        @method('PUT')

        {{-- Identité --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6 mb-6">

            <div class="flex items-center gap-4 mb-6">
                <div>
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Informations du compte</p>
                    <p class="text-sm text-slate-500">Ces informations sont visibles par les administrateurs.</p>
                </div>
            </div>

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

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
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

</div>

@endsection
