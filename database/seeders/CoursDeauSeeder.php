<?php

namespace Database\Seeders;

use App\Models\CoursDEau;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CoursDeauSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '-1');

        $this->command->info('Lecture du fichier GeoJSON (cela peut prendre un peu de temps)...');

        $json = File::get(storage_path('app/CoursEau_FXX.json'));
        $data = json_decode($json, true);

        if (!$data || !isset($data['features'])) {
            $this->command->error('Impossible de lire le fichier ou format invalide.');
            return;
        }

        $features = $data['features'];
        $total = count($features);
        $this->command->info($total . ' cours d\'eau trouvés. Début du filtrage et de l\'importation...');

        $bar = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        DB::disableQueryLog();
        DB::beginTransaction();

        try {
            foreach ($features as $feature) {
                $bar->advance();
                if (empty($feature['properties']['TopoOH'])) {
                    continue;
                }
                CoursDEau::create([
                    'nom'        => $feature['properties']['TopoOH'],
                    'type_cours' => 'rivière',
                    'trace'      => json_encode($feature['geometry']),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\nErreur lors de l'importation : " . $e->getMessage());
            return;
        }

        $bar->finish();
        $importes = CoursDEau::count();
        $this->command->info("\nSuccès ! $importes cours d'eau nommés ont été importés (les autres ont été ignorés).");
    }
}
