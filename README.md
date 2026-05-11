# Lymnik — Guide Architecture

Application Laravel 13 de surveillance de la qualité de l'eau.
Permet aux utilisateurs de saisir des analyses terrain, de visualiser les données sur une carte et de lancer des campagnes pédagogiques avec des groupes de participants.

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend CSS | Tailwind CSS v3 (JIT) via Vite |
| Frontend JS | Vanilla ES modules (Vite bundler) |
| Cartes | Leaflet.js |
| Graphiques | Chart.js |
| Base de données | MySQL (via XAMPP) |
| Authentification | Laravel Auth (session) |

---

## Répertoires clés

```
app/
├── Enums/
│   ├── Qualite.php          # Enum backed string : tres_bon, bon, passable, mediocre, mauvais
│   └── AnalyseType.php      # Enum backed string : bandelette, photometre, les_deux
├── Http/Controllers/
│   ├── AnalyseController    # CRUD analyses + calcul qualité (via QualiteService)
│   ├── CapteurController    # Supervision capteurs IoT
│   ├── CampagneController   # Gestion des campagnes pédagogiques
│   ├── DashboardController  # Vue admin vs utilisateur standard
│   ├── MapController        # Données pour la carte interactive
│   ├── ParticipantController# Sessions participants (sans compte)
│   └── StatistiqueController# Graphiques et export CSV/Excel
├── Http/Middleware/
│   ├── AuthOrParticipant    # Laisse passer auth() OU session participant
│   └── ParticipantSession   # Routes /session/* réservées aux participants
├── Models/
│   ├── Analyse              # mesures (JSON), qualite, est_valide, type
│   ├── Capteur              # Station IoT avec accesseurs turbidité/conductivité
│   ├── Campagne             # Session pédagogique avec code d'accès
│   ├── CoursDEau            # Rivière avec géométrie GeoJSON (colonne trace)
│   ├── Mesure               # Mesure IoT horodatée
│   ├── Point                # Lieu géographique d'une analyse
│   ├── SessionParticipant   # Participant dans une campagne (pseudo + groupe)
│   └── User                 # role: 'admin' | 'user'
├── Providers/
│   └── AppServiceProvider   # Gate 'admin' + View composer header (badge invalides)
└── Services/
    ├── CoursDEauService     # findNearest(lat, lng) via BBox + géométrie
    └── QualiteService       # calculerQualite() + isValid() — source de vérité PHP
```

---

## Flux de données principal

```
POST /analyse
  → AuthOrParticipant middleware
  → StoreAnalyseRequest (validation)
  → AnalyseController::store()
      → CoursDEauService::findNearest()   (si pas de cours d'eau fourni)
      → QualiteService::calculerQualite() (détermine la qualité globale)
      → QualiteService::isValid()         (détermine si dépasse les seuils)
      → Point::create() ou update()
      → Analyse::create()
  → redirect avec lat/lng
```

---

## Système de qualité de l'eau

**Source de vérité PHP :** `App\Services\QualiteService`
**Source de vérité JS :** `resources/js/core/config.js` (`getMesureQualite`, `QUALITE_CONFIG`)

> ⚠️ Les seuils de qualité existent en PHP ET en JavaScript. Si vous modifiez les seuils,
> mettez à jour **les deux fichiers** : `QualiteService::SEUILS` et `config.js` seuils.

**5 niveaux** (du meilleur au pire) :
```
tres_bon → bon → passable → mediocre → mauvais
```

La qualité globale d'une analyse = le **pire** des résultats individuels.

**Seuils pH** (logic spéciale) :
```
[6.5–8.5] = très bon | [6.0–9.0] = bon | [5.5–9.5] = passable | [5.0–10.0] = médiocre | autre = mauvais
```

---

## Rôles et permissions

| Rôle | Accès |
|------|-------|
| `admin` | Tout (backoffice, toutes analyses, capteurs, stats globales) |
| `user` | Ses analyses uniquement, ses campagnes, pas de backoffice |
| `participant` | Via code de campagne, session temporaire, saisie analyse |

**Gate définie :** `Gate::define('admin', ...)` dans `AppServiceProvider`.
**Middleware :** `AuthOrParticipant` pour les routes `/analyse/*`.

---

## Deux types d'authentification

1. **Utilisateur connecté** (`Auth::user()`) — route standard Laravel
2. **Participant** (`session('participant')`) — session temporaire via code campagne
   - Stocké en session : `id`, `pseudo`, `id_groupe`, `id_session`, `campagne_nom`
   - Accès via `/code` → `/session/map`, `/session/analyses`, etc.

---

## Architecture JavaScript

```
resources/js/
├── core/
│   ├── config.js        # QUALITE_CONFIG, MESURES_META, typeLabel(), qualiteBadgeHtml(), getMesureQualite()
│   ├── map-utils.js     # createBaseMap(), createCustomMarker() — wrappers Leaflet
│   ├── chart-utils.js   # CHART_FONTS, DEFAULT_TOOLTIP — config Chart.js partagée
│   └── ui.js            # qualiteBadgeHtml(), renderMesuresGrid() — HTML partagé entre pages
├── map.js               # Carte desktop + participant (markers, bottom sheet, geoloc)
├── analyses-desktop.js  # Page analyses/index : table, overlay, graphique par point
├── participant-analyses.js # Page /session/analyses : même logique, version mobile
├── dashboard.js         # Graphiques qualité + type sur /dashboard
├── statistiques.js      # Filtres avancés + 3 graphiques + export PNG
├── campagne-dashboard.js# Stats temps réel d'une campagne en cours
├── campagnes-gestion.js # Gestion admin des campagnes (édition, suppression)
├── header.js            # Overlay carrousel analyses invalides (admin seulement)
├── nouvelle-analyse.js  # Formulaire saisie analyse (géoloc, cours d'eau, photos)
└── ...
```

**Convention `window.__*` :** les données PHP → JS passent via des variables globales
déclarées dans un `<script>` en bas de page :
```blade
window.__RAW_DATA = @json($analyses);  // statistiques
window.mapPoints  = @json($pointsJson); // map
```

---

## Modèles — points d'attention

### Capteur
- `turbidite` et `conductivite` ont des **accesseurs** qui convertissent la valeur brute volt → NTU / µS/cm
- `latestMesure()` : relation `hasOne()->latestOfMany()`
- `quali_air` : qualité de l'air ambiante (nullable float)

### Analyse
- `mesures` est castée en `array` (JSON en base)
- Structure : `{ bandelette: {...}, photometre: {...}, note: "..." }`
- `est_valide` : `false` si dépasse les seuils de validité, nécessite validation admin
- `nom` : nom libre optionnel donné lors de la saisie

### CoursDEau
- `trace` : géométrie GeoJSON (MultiLineString ou LineString), souvent double-encodée
- `bbox_*` : bounding box précalculée pour accélérer `CoursDEauService::findNearest()`

---

## Composants Blade

```
resources/views/components/
└── quality-badge.blade.php   # <x-quality-badge :qualite="$q" size="md" />
                               # size: sm | md | lg
```

---

## Capteurs IoT

Les capteurs envoient des données via une API externe (LoRa/Bluetooth).
Les mesures sont stockées dans la table `mesures` (1 ligne = 1 relevé horodaté).
La table `capteurs` stocke la **dernière valeur connue** (dupliquée pour affichage rapide).

**devEUI** : identifiant LoRa
**UID** : identifiant Bluetooth

---

## Campagnes pédagogiques

Flux typique :
```
Enseignant crée une campagne → code 6 lettres généré
Élèves saisissent le code → session participant créée (pseudo + groupe)
Élèves saisissent des analyses sur la carte
Enseignant voit les résultats en temps réel (/campagnes/resultats)
```

Les groupes sont numérotés `0` (sans groupe), `1` (Groupe A), `2` (Groupe B), etc.

---

## Ajouter une nouvelle mesure au formulaire

1. Ajouter le champ dans `resources/views/analyse.blade.php`
2. Ajouter le key dans `StoreAnalyseRequest` (règles de validation)
3. Ajouter les seuils dans `QualiteService::SEUILS` ET dans `config.js` seuils
4. Ajouter le metadata dans `config.js` MESURES_META (label, unit, color)
5. Ajouter dans le tableau de correspondance du `StatistiqueController`

---

## Étendre les rôles

Le rôle est un simple string sur `users.role`. Pour ajouter un rôle :
1. Définir un nouveau `Gate::define(...)` dans `AppServiceProvider`
2. Utiliser `@can('nouveau-role')` en blade ou `Gate::allows(...)` en controller
3. Pas d'Enum pour le rôle actuellement — garder cohérence avec 'admin' / 'user'
