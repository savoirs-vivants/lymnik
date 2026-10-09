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
                    'type'        => $c->type,
                    'description' => $c->description,
                    'image'       => $c->image ? asset('storage/' . $c->image) : null,
                    'images'      => collect($c->images ?? [])->map(fn($img) => asset('storage/' . $img))->values(),
                    'date'        => $c->date
                                     ? \Carbon\Carbon::parse($c->date)->translatedFormat('d M Y')
                                     : $c->created_at?->translatedFormat('d M Y'),
                    'date_raw'    => $c->date ? \Carbon\Carbon::parse($c->date)->format('Y-m-d') : null,
                ])
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lat'     => 'required|numeric|between:-90,90',
            'lng'     => 'required|numeric|between:-180,180',
            'type'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'nullable|date',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:15360',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('coulees', 'public');
            }
        }

        $coulée = CouleeDeBoue::create([
            'lat'         => $data['lat'],
            'lng'         => $data['lng'],
            'user_id'     => Auth::id(),
            'type'        => $data['type'] ?? null,
            'description' => $data['description'] ?? null,
            'date'        => $data['date'] ?? null,
            'image'       => $imagePaths[0] ?? null,
            'images'      => $imagePaths,
        ]);

        return response()->json([
            'id'          => $coulée->id,
            'lat'         => (float) $coulée->lat,
            'lng'         => (float) $coulée->lng,
            'user'        => trim(Auth::user()->firstname . ' ' . Auth::user()->name),
            'user_id'     => $coulée->user_id,
            'type'        => $coulée->type,
            'description' => $coulée->description,
            'date'        => $coulée->date ? \Carbon\Carbon::parse($coulée->date)->translatedFormat('d M Y') : $coulée->created_at->translatedFormat('d M Y'),
            'image'       => $coulée->image ? asset('storage/' . $coulée->image) : null,
            'images'      => collect($coulée->images ?? [])->map(fn($img) => asset('storage/' . $img))->values(),
            'date_raw'    => $coulée->date ? \Carbon\Carbon::parse($coulée->date)->format('Y-m-d') : null,
        ], 201);
    }

    public function update(Request $request, CouleeDeBoue $couleeDeBoue)
    {
        if (Auth::user()->role !== 'admin' && $couleeDeBoue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Interdit'], 403);
        }

        $data = $request->validate([
            'type'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'nullable|date',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:15360',
        ]);

        $imagePaths = $couleeDeBoue->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('coulees', 'public');
            }
        }

        $couleeDeBoue->update([
            'type'        => $data['type'] ?? $couleeDeBoue->type,
            'description' => $data['description'] ?? $couleeDeBoue->description,
            'date'        => $data['date'] ?? $couleeDeBoue->date,
            'image'       => $imagePaths[0] ?? null,
            'images'      => $imagePaths,
        ]);

        return response()->json([
            'id'          => $couleeDeBoue->id,
            'lat'         => (float) $couleeDeBoue->lat,
            'lng'         => (float) $couleeDeBoue->lng,
            'user'        => trim($couleeDeBoue->user->firstname . ' ' . $couleeDeBoue->user->name),
            'user_id'     => $couleeDeBoue->user_id,
            'type'        => $couleeDeBoue->type,
            'description' => $couleeDeBoue->description,
            'date'        => $couleeDeBoue->date ? \Carbon\Carbon::parse($couleeDeBoue->date)->translatedFormat('d M Y') : $couleeDeBoue->created_at->translatedFormat('d M Y'),
            'date_raw'    => $couleeDeBoue->date ? \Carbon\Carbon::parse($couleeDeBoue->date)->format('Y-m-d') : null,
            'image'       => $couleeDeBoue->image ? asset('storage/' . $couleeDeBoue->image) : null,
            'images'      => collect($couleeDeBoue->images ?? [])->map(fn($img) => asset('storage/' . $img))->values(),
        ]);
    }

    public function destroy(CouleeDeBoue $couleeDeBoue)
    {
        if (Auth::user()->role !== 'admin' && $couleeDeBoue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Interdit'], 403);
        }

        $couleeDeBoue->delete();

        return response()->json(['success' => true]);
    }
}
