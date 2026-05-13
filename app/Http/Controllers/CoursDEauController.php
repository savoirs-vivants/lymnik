<?php

namespace App\Http\Controllers;

use App\Models\CoursDEau;
use App\Services\CoursDEauService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursDEauController extends Controller
{
    public function nearest(Request $request, CoursDEauService $service): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $river = $service->findNearest((float) $validated['lat'], (float) $validated['lng']);

        if (! $river) {
            return response()->json(null);
        }

        return response()->json([
            'id'  => $river->id,
            'nom' => $river->nom,
        ]);
    }

    public function traces(Request $request): JsonResponse
    {
        $ids = array_filter(array_map('intval', explode(',', $request->query('ids', ''))));

        if (empty($ids)) {
            return response()->json([]);
        }

        $rivers = CoursDEau::whereIn('id', $ids)
            ->select(['id', 'nom', 'trace'])
            ->get()
            ->map(fn($r) => [
                'id'       => $r->id,
                'nom'      => $r->nom,
                'geometry' => ($decoded = json_decode($r->trace, true)) && is_string($decoded)
                    ? json_decode($decoded, true)
                    : $decoded,
            ]);

        return response()->json($rivers);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = CoursDEau::where('nom', 'LIKE', '%' . $q . '%')
            ->orderBy('nom')
            ->limit(8)
            ->get(['id', 'nom', 'type_cours']);

        return response()->json($results);
    }
}
