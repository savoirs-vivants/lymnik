@extends('layouts.desktop')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', count($users) . ' compte' . (count($users) > 1 ? 's' : '') . ' enregistré' . (count($users) >
    1 ? 's' : ''))

@section('content')

    @if (session('success'))
        <div
            class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-3 rounded-xl">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div
            class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3 rounded-xl">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(34,42,96,0.07)] border border-slate-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="flex-1">
                <h2 class="text-[14px] font-bold text-sv-blue font-grotesk">Comptes utilisateurs</h2>
                <p class="text-[11px] text-slate-400 mt-0.5 font-grotesk">Modifier les informations ou supprimer un compte
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th
                            class="text-left px-6 py-3 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            Utilisateur</th>
                        <th
                            class="text-left px-6 py-3 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            Email</th>
                        <th
                            class="text-left px-6 py-3 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            Rôle</th>
                        <th
                            class="text-left px-6 py-3 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            Inscrit le</th>
                        <th
                            class="text-right px-6 py-3 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $user)
                        <tr id="row-{{ $user->id }}" class="group hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-sv-blue flex items-center justify-center text-white text-[12px] font-bold font-grotesk flex-shrink-0">
                                        {{ strtoupper(substr($user->firstname, 0, 1) . substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-[13px] font-grotesk">{{ $user->firstname }}
                                            {{ $user->name }}</p>
                                        @if ($user->id === auth()->id())
                                            <span class="text-[10px] font-mono font-bold text-sv-green">Vous</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-[13px] font-grotesk">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-sv-blue/10 text-sv-blue',
                                        'moderateur' => 'bg-sv-green/10 text-sv-green',
                                        'utilisateur' => 'bg-slate-100 text-slate-500',
                                    ];
                                    $role = $user->role ?? 'utilisateur';
                                    $cls = $roleColors[$role] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono {{ $cls }}">
                                    {{ ucfirst($role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[12px] font-mono">
                                {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($user->id !== auth()->id())
                                        <a href="#edit-{{ $user->id }}"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-sv-blue/5 hover:bg-sv-blue/10 text-sv-blue text-[12px] font-bold font-grotesk rounded-lg transition-colors cursor-pointer no-underline">
                                            <svg width="13" height="13" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Modifier
                                        </a>

                                        <a href="#delete-{{ $user->id }}"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-500 text-[12px] font-bold font-grotesk rounded-lg transition-colors cursor-pointer no-underline">
                                            <svg width="13" height="13" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Supprimer
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <tr id="edit-{{ $user->id }}"
                            class="hidden target:table-row bg-sv-blue/5 border-b border-sv-blue/10">
                            <td colspan="5" class="px-6 py-5">
                                <form method="POST" action="{{ route('desktop.backoffice.update', $user) }}"
                                    class="flex flex-wrap items-end gap-4">
                                    @csrf @method('PUT')
                                    <div class="flex flex-col gap-1 min-w-[160px]">
                                        <label
                                            class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Prénom</label>
                                        <input type="text" name="firstname"
                                            value="{{ old('firstname', $user->firstname) }}"
                                            class="h-9 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 font-grotesk focus:outline-none focus:ring-2 focus:ring-sv-blue/20 focus:border-sv-blue/40 transition-all">
                                    </div>
                                    <div class="flex flex-col gap-1 min-w-[160px]">
                                        <label
                                            class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Nom</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                            class="h-9 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 font-grotesk focus:outline-none focus:ring-2 focus:ring-sv-blue/20 focus:border-sv-blue/40 transition-all">
                                    </div>
                                    <div class="flex flex-col gap-1 min-w-[220px]">
                                        <label
                                            class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                            class="h-9 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-sv-blue/20 focus:border-sv-blue/40 transition-all">
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <label
                                            class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Rôle</label>
                                        <select name="role"
                                            class="h-9 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 font-grotesk focus:outline-none focus:ring-2 focus:ring-sv-blue/20 focus:border-sv-blue/40 transition-all cursor-pointer">
                                            @foreach (['utilisateur', 'moderateur', 'admin'] as $r)
                                                <option value="{{ $r }}"
                                                    {{ ($user->role ?? 'utilisateur') === $r ? 'selected' : '' }}>
                                                    {{ ucfirst($r) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-2 pb-0.5">
                                        <button type="submit"
                                            class="h-9 px-4 bg-sv-blue hover:bg-sv-blue/90 text-white text-[13px] font-bold font-grotesk rounded-lg transition-colors cursor-pointer border-none outline-none">
                                            Enregistrer
                                        </button>
                                        <a href="#row-{{ $user->id }}"
                                            class="flex items-center h-9 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-bold font-grotesk rounded-lg transition-colors cursor-pointer no-underline">
                                            Annuler
                                        </a>
                                    </div>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400 text-sm font-mono">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($users as $user)
        @if ($user->id !== auth()->id())
            <div id="delete-{{ $user->id }}"
                class="hidden target:flex fixed inset-0 z-50 items-center justify-center p-4">
                <a href="#row-{{ $user->id }}"
                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm cursor-default"></a>

                <div class="relative bg-white rounded-2xl shadow-[0_24px_60px_rgba(34,42,96,0.2)] p-6 w-full max-w-sm">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg width="22" height="22" fill="none" stroke="#ef4444" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[15px] font-bold text-slate-900 font-grotesk">Supprimer l'utilisateur</h3>
                            <p class="text-[13px] text-slate-500 mt-0.5 font-grotesk">Cette action est irréversible.</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-5 font-grotesk">
                        Voulez-vous vraiment supprimer le compte de <strong class="text-slate-800">{{ $user->firstname }}
                            {{ $user->name }}</strong> ?
                    </p>
                    <div class="flex gap-3">
                        <a href="#row-{{ $user->id }}"
                            class="flex-1 flex items-center justify-center h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-bold font-grotesk transition-colors cursor-pointer no-underline">
                            Annuler
                        </a>
                        <form method="POST" action="{{ route('desktop.backoffice.destroy', $user) }}" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full h-10 rounded-xl bg-red-500 hover:bg-red-600 text-white text-[13px] font-bold font-grotesk transition-colors cursor-pointer border-none outline-none">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection
