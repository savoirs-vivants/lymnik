<?php

namespace App\Services;

use App\Enums\Qualite;

class QualiteService
{
    // Valeurs max au-delà desquelles une analyse est signalée comme "invalide" et mise
    // en attente de validation admin. Ces seuils correspondent aux limites physiques des
    // appareils de mesure (bandelette JBL et photomètre), pas aux normes de qualité de l'eau.
    // Une valeur dépassant ces seuils indique probablement une erreur de saisie ou un capteur défaillant.
    private const SEUILS_VALIDITE = [
        'bandelette' => [
            'nitrates'      => 50,
            'nitrites'      => 1,
            'durete_totale' => 375,
            'durete_carb'   => 357,
            'ph'            => 20,
            'chlore'        => 500,
        ],
        'photometre' => [
            'ammoniaque' => 5,
            'nitrate'    => 50,
            'phosphate'  => 1,
        ],
    ];

    // Seuils de qualité écologique par paramètre (mg/L sauf pH).
    // Format : [très_bon, bon, passable, médiocre] — au-delà du 4e seuil = mauvais.
    // Source : référentiels DCE (Directive Cadre sur l'Eau) adaptés aux usages pédagogiques.
    // ⚠️ Ces valeurs sont dupliquées dans resources/js/core/config.js (getMesureQualite).
    //    Toute modification ici doit être répercutée dans le fichier JS.
    private const SEUILS_QUALITE = [
        'nitrites'   => [0.03, 0.3,  0.5,  1.0],
        'nitrates'   => [2,    10,   25,   50],
        'nitrate'    => [2,    10,   25,   50],
        'phosphate'  => [0.05, 0.2,  0.5,  1.0],
        'chlore'     => [25,   50,   100,  250],
        'ammoniaque' => [0.1,  0.5,  2.0,  5.0],
    ];

    // La qualité globale d'une analyse est le pire résultat parmi toutes les mesures :
    // une seule valeur médiocre suffit à déclasser l'ensemble, même si les autres sont bonnes.
    public function calculer(array $mesures): string
    {
        $qualite = Qualite::TresBon;

        $toutes = array_merge($mesures['bandelette'] ?? [], $mesures['photometre'] ?? []);

        foreach ($toutes as $key => $val) {
            if ($val === null) continue;
            $v = (float) $val;
            $q = $this->evaluer($key, $v);
            if ($q && $q->severity() > $qualite->severity()) {
                $qualite = $q;
            }
        }

        return $qualite->value;
    }

    public function isValid(array $mesures): bool
    {
        foreach (self::SEUILS_VALIDITE as $type => $seuils) {
            foreach ($seuils as $key => $max) {
                $val = $mesures[$type][$key] ?? null;
                if ($val !== null && (float) $val > $max) {
                    return false;
                }
            }
        }
        return true;
    }

    // Le pH suit une logique d'intervalle (ni trop acide ni trop basique) alors que
    // tous les autres paramètres sont des seuils croissants unilatéraux.
    // C'est pourquoi il est traité séparément avec des conditions bilatérales.
    private function evaluer(string $key, float $v): ?Qualite
    {
        if ($key === 'ph') {
            if ($v >= 6.5 && $v <= 8.5)      return Qualite::TresBon;
            if ($v >= 6.0 && $v <= 9.0)      return Qualite::Bon;
            if ($v >= 5.5 && $v <= 9.5)      return Qualite::Passable;
            if ($v >= 5.0 && $v <= 10.0)     return Qualite::Mediocre;
            return Qualite::Mauvais;
        }

        if (isset(self::SEUILS_QUALITE[$key])) {
            [$s1, $s2, $s3, $s4] = self::SEUILS_QUALITE[$key];
            if ($v <= $s1) return Qualite::TresBon;
            if ($v <= $s2) return Qualite::Bon;
            if ($v <= $s3) return Qualite::Passable;
            if ($v <= $s4) return Qualite::Mediocre;
            return Qualite::Mauvais;
        }

        // Paramètre non reconnu (ex: champ custom) → pas de contribution à la qualité globale
        return null;
    }
}
