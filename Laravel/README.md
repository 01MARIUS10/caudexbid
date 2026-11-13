# caudexbid — API Laravel

Site d'enchères de plantes à date butoir. Schéma : `../BDD/minimal.dbml`.

## Lancer avec Docker (depuis le dossier parent)

```bash
cd ..
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
```

- Back office : http://localhost:8088/backoffice (compte `admin` / `password`)
- API : http://localhost:8088/api (port modifiable via `APP_PORT`)
- Après une modification des vues ou du CSS : `npm run build` (Tailwind 4 + Vite)
- MySQL : localhost:3307 — base / user / mot de passe `caudexbid`
- Le service `scheduler` lance `php artisan auctions:close` chaque minute (clôture des lots).

Comptes de démo (mot de passe `password`) : `admin` (A), `andy88` / `mika_p` (P),
`kenji` / `lina` / `minho` (M), `chen` / `hiro` / `sora` (G).

## Architecture

```
app/Modules/
  User/      _UserAuth, _UserProfil, _UserGrad
  Product/   (cœur : produits, sessions, propriétaires, documents)
             _ProductMedia, _ProductBid, _ProductAchat
  Message/
app/Shared/  tableau de bord du back office, exceptions, helpers
app/Http/Controllers/Site + resources/views/site + routes/web.php : site public (/), hors modules
```

Chaque (sous-)module — les sous-modules sont préfixés par `_` :

```
{Module}/
  Actions/{Action}/   XxxInput, XxxValidation, XxxAction
  Repository/         accès aux données
  Models/             modèles Eloquent + Enums
  Controllers/        Api/ · BackOffice/
  routes/             api.php (/api) · backoffice.php (/backoffice)
  Resources/          Resources JSON de l'API (ProductCardResource…)
  Resources/views/    vues Blade du module (namespace : product::, product-bid::, user-auth::…)
  Middleware/         (_UserGrad uniquement) middleware `grade:`
```

- `XxxInput` — objet immuable des données d'entrée ;
- `XxxValidation` — règles de validation + droits, construit l'Input ;
- `XxxAction` — logique métier (`execute(XxxInput)`).

`App\Providers\ModuleServiceProvider` enregistre les vues de chaque module et liste ses fichiers de routes
(chargés par `routes/api.php` et `routes/web.php`). Les composants Blade partagés (`x-bo.*`) sont dans
`resources/views/components`.

Règles métier paramétrables : `config/caudexbid.php` (48 h, soft close, médias, répartition 30/30/40).

Droits par grade : chaque route déclare sa combinaison de grades, par exemple
`Route::middleware(['auth', EnsureGrade::for(GradeCode::Premium, GradeCode::Admin)])`
(`_UserGrad/Middleware/EnsureGrade` + `_UserGrad/Models/Enums/GradeCode`).
Connexion au back office : `BackOfficeLoginAction::ALLOWED_GRADES` (aujourd'hui : admin).
