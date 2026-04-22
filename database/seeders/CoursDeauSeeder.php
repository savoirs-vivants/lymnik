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
        $this->command->info($total . ' cours d\'eau trouvés. Début de l\'importation...');

        $bar = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        DB::disableQueryLog();

        DB::beginTransaction();

        try {
            foreach ($features as $feature) {
                $nom = $feature['properties']['nom_cours_eau']
                    ?? $feature['properties']['name']
                    ?? 'Cours d\'eau inconnu';

                CoursDEau::create([
                    'nom' => $nom,
                    'type_cours' => 'rivière',
                    'trace' => $feature['geometry'],
                ]);

                $bar->advance();
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\nErreur lors de l'importation : " . $e->getMessage());
            return;
        }

        $bar->finish();
        $this->command->info("\nLes cours d'eau ont été importés avec succès !");
    }
}
