<?php

namespace App\Console\Commands;

use App\Models\CoursDEau;
use Illuminate\Console\Command;

class PopulateCoursDEauBbox extends Command
{
    protected $signature   = 'cours-d-eau:populate-bbox';
    protected $description = 'Calcule et stocke les bounding boxes des géométries';

    public function handle(): int
    {
        $total = CoursDEau::count();
        $bar   = $this->output->createProgressBar($total);
        $bar->start();

        CoursDEau::select(['id', 'trace'])
            ->orderBy('id')
            ->chunk(500, function ($rivers) use ($bar) {
                $updates = [];

                foreach ($rivers as $river) {
                    try {
                        $geo = json_decode($river->trace, true);
                        if (is_string($geo)) {
                            $geo = json_decode($geo, true);
                        }
                        if (! $geo || ! isset($geo['coordinates'])) {
                            $bar->advance();
                            continue;
                        }

                        $allCoords = $geo['type'] === 'MultiLineString'
                            ? array_merge(...$geo['coordinates'])
                            : $geo['coordinates'];

                        if (empty($allCoords)) {
                            $bar->advance();
                            continue;
                        }

                        $lngs = array_column($allCoords, 0);
                        $lats = array_column($allCoords, 1);

                        $updates[] = [
                            'id'          => $river->id,
                            'bbox_min_lng' => min($lngs),
                            'bbox_max_lng' => max($lngs),
                            'bbox_min_lat' => min($lats),
                            'bbox_max_lat' => max($lats),
                        ];
                    } catch (\Throwable $e) {
                        $this->warn("\nErreur ID {$river->id} : {$e->getMessage()}");
                    }
                    $bar->advance();
                }

                if (empty($updates)) return;

                $ids  = array_column($updates, 'id');
                $cols = ['bbox_min_lng', 'bbox_max_lng', 'bbox_min_lat', 'bbox_max_lat'];
                $cases = array_fill_keys($cols, '');

                foreach ($updates as $u) {
                    foreach ($cols as $col) {
                        $cases[$col] .= "WHEN {$u['id']} THEN {$u[$col]} ";
                    }
                }

                $inList = implode(',', $ids);
                $sql    = "UPDATE cours_d_eaus SET "
                    . implode(', ', array_map(
                        fn($col) => "`{$col}` = CASE `id` {$cases[$col]} END",
                        $cols
                    ))
                    . " WHERE `id` IN ({$inList})";

                \Illuminate\Support\Facades\DB::statement($sql);
            });

        $bar->finish();
        $this->newLine();
        $this->info('Bounding boxes peuplées avec succès.');
        return 0;
    }
}
