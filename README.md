# Lymnik

**Plateforme de suivi de la qualité de l'eau** — terrain, IoT et pédagogie.

Lymnik permet à des utilisateurs de terrain de saisir des analyses physico-chimiques de cours d'eau, de visualiser les résultats sur une carte interactive, de superviser des capteurs IoT en temps réel, et d'organiser des campagnes pédagogiques avec des groupes d'élèves.

---

## Fonctionnalités principales

### Analyse terrain
Les utilisateurs saisissent des mesures directement sur le terrain depuis leur mobile. Deux types de dispositifs sont supportés : la bandelette réactive JBL et le photomètre. Les analyses sont géolocalisées, rattachées automatiquement au cours d'eau le plus proche, et évaluées selon un système de qualité en 5 niveaux (très bon → mauvais). Toute analyse dépassant les seuils maximaux est marquée invalide et placée en attente de validation admin.

### Carte interactive
Une carte Leaflet affiche tous les points d'analyse existants avec leur niveau de qualité. L'utilisateur peut cliquer sur n'importe quel endroit pour démarrer une nouvelle analyse depuis cet emplacement, ou cliquer sur un marqueur existant pour consulter l'historique du point.

### Capteurs IoT
Des capteurs terrain (LoRa/Bluetooth) remontent en continu des mesures de turbidité, conductivité, température de l'eau, hauteur, débit et qualité de l'air. Les données brutes sont converties automatiquement via des accesseurs de modèle (volt → NTU, volt → µS/cm avec compensation thermique). L'interface de supervision affiche la dernière mesure connue et l'historique graphique de chaque capteur.

### Campagnes pédagogiques
Un enseignant crée une campagne et reçoit un code d'accès à partager avec ses élèves. Chaque élève rejoint sans créer de compte — uniquement via le code et un pseudo. Les élèves sont répartis en groupes, saisissent des analyses sur la carte, et l'enseignant consulte les résultats en temps réel avec des graphiques comparatifs par groupe.

### Statistiques et export
Une page de statistiques avancées permet de filtrer les analyses par cours d'eau, ville, point, campagne et groupe, de comparer les mesures entre groupes sur un graphique en barres, et d'exporter le graphique en PNG.

### Backoffice admin
L'administrateur peut valider les analyses suspectes, gérer les comptes utilisateurs (création, modification, suppression), consulter les statistiques de contribution de chaque utilisateur, et superviser l'ensemble des analyses.

---

## Stack technique

| Couche | Technologie | Version |
|---|---|---|
| Langage backend | PHP | ^8.3 |
| Framework backend | Laravel | ^13.0 |
| Base de données | MySQL | 5.7+ |
| Serveur local | XAMPP (Apache + MySQL) | — |
| CSS | Tailwind CSS | ^4.2 |
| Bundler | Vite | ^8.0 |
| Cartes | Leaflet.js | 1.9.4 (CDN) |
| Graphiques | Chart.js | dernière (CDN) |
| Polices | Space Grotesk / Space Mono | Google Fonts |

Le frontend est entièrement en **Vanilla JS ES modules** — aucun framework JS (pas de Vue, React, etc.).

---

## Rôles et permissions

| Rôle | Qui | Accès |
|---|---|---|
| `admin` | Administrateur de la plateforme | Tout : backoffice, validation analyses, capteurs, statistiques globales, toutes les campagnes |
| `user` | Utilisateur inscrit | Ses propres analyses, ses campagnes, la carte, les statistiques filtrées sur ses données |
| `participant` | Élève sans compte | Accès via code de campagne uniquement : saisie d'analyses, carte de session, résultats de sa campagne |

Les permissions admin sont gérées via le système de **Gates Laravel** (`Gate::define('admin', ...)`). Les participants s'authentifient via une session temporaire (`session('participant')`) distincte du système Auth standard.

---

## Prérequis

Avant d'installer Lymnik, assurez-vous d'avoir les éléments suivants sur votre machine :

| Outil | Version minimale | Vérification |
|---|---|---|
| PHP | 8.3 | `php -v` |
| Composer | 2.x | `composer --version` |
| Node.js | 18+ | `node -v` |
| npm | 9+ | `npm -v` |
| MySQL | 5.7+ | via XAMPP ou serveur dédié |
| Git | — | `git --version` |

> **Environnement recommandé :** XAMPP sous Windows ou Linux, avec Apache + MySQL activés.  
> Une configuration alternative avec Laravel Herd, Laragon ou Docker est possible.

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-org/lymnik.git
cd lymnik
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances Node

```bash
npm install
```

### 4. Configurer l'environnement

Copiez le fichier d'exemple et ouvrez-le pour le modifier :

```bash
cp .env.example .env
```

Éditez `.env` avec vos valeurs :

```env
APP_NAME=Lymnik
APP_ENV=local
APP_KEY=                        # généré à l'étape suivante
APP_DEBUG=true
APP_URL=http://localhost/Lymnik/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lymnik
DB_USERNAME=root
DB_PASSWORD=                    # laisser vide sur XAMPP par défaut

MAIL_MAILER=log                 # "log" pour le dev, "smtp" pour la prod
MAIL_FROM_ADDRESS="noreply@lymnik.fr"
MAIL_FROM_NAME="Lymnik"
```

> **Note XAMPP :** le dossier du projet doit se trouver dans `C:\xampp\htdocs\Lymnik\`.  
> L'`APP_URL` doit pointer vers `http://localhost/Lymnik/public`.

### 5. Générer la clé d'application

```bash
php artisan key:generate
```

### 6. Créer la base de données

Ouvrez phpMyAdmin (`http://localhost/phpmyadmin`) et créez une base de données nommée `lymnik` (encodage `utf8mb4_unicode_ci`).

### 7. Lancer les migrations

```bash
php artisan migrate
```

### 8. Créer le lien de stockage public

Nécessaire pour l'affichage des photos d'analyses :

```bash
php artisan storage:link
```

### 9. Compiler les assets frontend

**Mode développement** (avec hot-reload) :
```bash
npm run dev
```

**Mode production** (build optimisé) :
```bash
npm run build
```

---

## Lancer l'application

### Avec XAMPP

1. Démarrez **Apache** et **MySQL** depuis le panneau XAMPP.
2. Accédez à `http://localhost/Lymnik/public`.

> Si vous utilisez Laravel Valet ou Herd, l'URL sera simplement `http://lymnik.test`.

### Avec le serveur intégré Laravel

```bash
php artisan serve
```

Puis ouvrez `http://127.0.0.1:8000`.

> Dans ce cas, `npm run dev` doit tourner en parallèle dans un second terminal pour servir les assets.

---

## Configuration de l'envoi d'e-mails

Le système de réinitialisation de mot de passe nécessite une configuration mail. En développement, les e-mails sont écrits dans `storage/logs/laravel.log` (`MAIL_MAILER=log`). Pour la production, configurez un serveur SMTP dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-fournisseur.com
MAIL_PORT=587
MAIL_USERNAME=votre@email.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@lymnik.fr"
MAIL_FROM_NAME="Lymnik"
```

Services compatibles : Mailtrap (dev/test), Mailgun, Postmark, Gmail SMTP, Amazon SES.

---

## Créer le premier compte administrateur

Après migration, créez un utilisateur via la page `/register`, puis passez son rôle à `admin` directement en base :

```sql
UPDATE users SET role = 'admin' WHERE email = 'votre@email.com';
```

Ou via Tinker :

```bash
php artisan tinker
```
```php
\App\Models\User::where('email', 'votre@email.com')->update(['role' => 'admin']);
```

---

## Structure du projet

```
app/
├── Enums/                  # Types PHP 8.1 : Qualite, AnalyseType
├── Http/
│   ├── Controllers/        # Un controller par domaine métier
│   ├── Middleware/         # AuthOrParticipant, ParticipantSession
│   └── Requests/           # Form Requests avec règles de validation
├── Models/                 # Eloquent : User, Analyse, Point, CoursDEau, Capteur...
├── Services/               # QualiteService, CoursDEauService
└── Support/                # QualiteConfig (centrales des couleurs/labels)

resources/
├── js/
│   ├── core/               # Modules partagés : config.js, map-utils.js, chart-utils.js
│   └── *.js                # Un fichier par page (map, analyses-desktop, statistiques...)
└── views/
    ├── auth/               # login, register, forgot-password, reset-password
    ├── desktop/            # Layout desktop (dashboard, analyses, capteurs, stats...)
    ├── mobile/             # Interface terrain (carte mobile, mes analyses)
    ├── participant/        # Interface session campagne (sans compte)
    ├── components/         # Composants Blade réutilisables (quality-badge)
    └── emails/             # Templates e-mails transactionnels

database/
└── migrations/             # Toutes les migrations dans l'ordre chronologique
```
