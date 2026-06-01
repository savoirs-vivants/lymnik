<?php

namespace App\Http\Controllers;

use App\Models\CouleeDeBoue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouleeDeBoueController extends Controller
{
    public function index()
    {
        return response()->json(
            CouleeDeBoue::with('user:id,firstname,name')
                ->get()
                ->map(fn($c) => [
                    'id'   => $c->id,
                    'lat'  => (float) $c->lat,
                    'lng'  => (float) $c->lng,
                    'user' => trim($c->user?->firstname . ' ' . $c->user?->name),
                    'date' => $c->created_at?->translatedFormat('d M Y'),
                ])
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $coulée = CouleeDeBoue::create([
            'lat'     => $data['lat'],
            'lng'     => $data['lng'],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'id'   => $coulée->id,
            'lat'  => (float) $coulée->lat,
            'lng'  => (float) $coulée->lng,
            'user' => trim(Auth::user()->firstname . ' ' . Auth::user()->name),
            'date' => $coulée->created_at->translatedFormat('d M Y'),
        ], 201);
    }
}
