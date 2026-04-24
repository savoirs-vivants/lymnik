<?php

namespace App\Services;

use App\Models\CoursDEau;

class CoursDEauService
{
    private const SEARCH_RADIUS = 0.15;
    private const MAX_SNAP_DIST = 0.02;

    public function findNearest(float $lat, float $lng): ?CoursDEau
    {
        $r = self::SEARCH_RADIUS;

        $candidates = CoursDEau::whereNotNull('bbox_min_lat')
            ->where('bbox_max_lat', '>=', $lat - $r)
            ->where('bbox_min_lat', '<=', $lat + $r)
            ->where('bbox_max_lng', '>=', $lng - $r)
            ->where('bbox_min_lng', '<=', $lng + $r)
            ->select(['id', 'nom', 'trace'])
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $nearest = null;
        $minDist  = PHP_FLOAT_MAX;

        foreach ($candidates as $river) {
            $geo = json_decode($river->trace, true);
            if (is_string($geo)) {
                $geo = json_decode($geo, true);
            }

            if (! $geo || ! isset($geo['coordinates'])) {
                continue;
            }

            $coords = $geo['type'] === 'MultiLineString'
                ? array_merge(...$geo['coordinates'])
                : $geo['coordinates'];

            $dist = $this->distanceToLineString($lat, $lng, $coords);

            if ($dist < $minDist) {
                $minDist = $dist;
                $nearest = $river;
            }
        }

        return ($nearest !== null && $minDist <= self::MAX_SNAP_DIST) ? $nearest : null;
    }

    private function distanceToLineString(float $lat, float $lng, array $coords): float
    {
        $minDist = PHP_FLOAT_MAX;
        $n       = count($coords);

        for ($i = 0; $i < $n - 1; $i++) {
            $d = $this->pointToSegmentDist(
                $lng,
                $lat,
                $coords[$i][0],
                $coords[$i][1],
                $coords[$i + 1][0],
                $coords[$i + 1][1]
            );
            if ($d < $minDist) {
                $minDist = $d;
            }
        }

        return $minDist;
    }

    private function pointToSegmentDist(
        float $px,
        float $py,
        float $ax,
        float $ay,
        float $bx,
        float $by
    ): float {
        $cosLat = cos(deg2rad(($py + $ay + $by) / 3));

        $dx = ($bx - $ax) * $cosLat;
        $dy = $by - $ay;

        $qx = ($px - $ax) * $cosLat;
        $qy = $py - $ay;

        if ($dx === 0.0 && $dy === 0.0) {
            return sqrt($qx ** 2 + $qy ** 2);
        }

        $t = ($qx * $dx + $qy * $dy) / ($dx ** 2 + $dy ** 2);
        $t = max(0.0, min(1.0, $t));

        return sqrt(($qx - $t * $dx) ** 2 + ($qy - $t * $dy) ** 2);
    }
}
