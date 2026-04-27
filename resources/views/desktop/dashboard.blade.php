@extends('layouts.desktop')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de la plateforme')

@section('content')

<div class="grid grid-cols-3 gap-6">

    @php
        $stats = [
            ['label' => 'Utilisateurs', 'value' => \App\Models\User::count(), 'color' => 'text-[#222a60]', 'bg' => 'bg-[#222a60]/8'],
            ['label' => 'Analyses',     'value' => \App\Models\Analyse::count(), 'color' => 'text-sv-green', 'bg' => 'bg-sv-green/8'],
            ['label' => 'Points',       'value' => \App\Models\Point::count(),   'color' => 'text-blue-500',  'bg' => 'bg-blue-50'],
        ];
    @endphp

    @foreach ($stats as $s)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
            <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-3">{{ $s['label'] }}</p>
            <p class="text-4xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
        </div>
    @endforeach

</div>

@endsection
