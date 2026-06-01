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
                    'id'      => $c->id,
                    'lat'     => (float) $c->lat,
                    'lng'     => (float) $c->lng,
                    'user'    => trim($c->user?->firstname . ' ' . $c->user?->name),
                    'user_id' => $c->user_id,
                    'type'    => $c->type,
                    'image'   => $c->image ? asset('storage/' . $c->image) : null,
                    'date'    => $c->date
                                 ? \Carbon\Carbon::parse($c->date)->translatedFormat('d M Y')
                                 : $c->created_at?->translatedFormat('d M Y'),
                ])
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lat'   => 'required|numeric|between:-90,90',
            'lng'   => 'required|numeric|between:-180,180',
            'type'  => 'nullable|string|max:255',
            'date'  => 'nullable|date',
            'image' => 'nullable|image|max:15360',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('coulees', 'public');
        }

        $coulée = CouleeDeBoue::create([
            'lat'     => $data['lat'],
            'lng'     => $data['lng'],
            'user_id' => Auth::id(),
            'type'    => $data['type'] ?? null,
            'date'    => $data['date'] ?? null,
            'image'   => $imagePath,
        ]);

        return response()->json([
            'id'      => $coulée->id,
            'lat'     => (float) $coulée->lat,
            'lng'     => (float) $coulée->lng,
            'user'    => trim(Auth::user()->firstname . ' ' . Auth::user()->name),
            'user_id' => $coulée->user_id,
            'type'    => $coulée->type,
            'date'    => $coulée->date ? \Carbon\Carbon::parse($coulée->date)->translatedFormat('d M Y') : $coulée->created_at->translatedFormat('d M Y'),
            'image'   => $coulée->image ? asset('storage/' . $coulée->image) : null,
        ], 201);
    }

    public function destroy(CouleeDeBoue $couleeDeBoue)
    {
        if ($couleeDeBoue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Interdit'], 403);
        }

        $couleeDeBoue->delete();

        return response()->json(['success' => true]);
    }
}
