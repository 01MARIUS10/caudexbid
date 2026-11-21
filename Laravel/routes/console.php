<?php

use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsAction;
use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsValidation;
use App\Modules\Product\Actions\RelistProducts\RelistProductsAction;
use App\Modules\Product\Actions\RelistProducts\RelistProductsValidation;
use App\Modules\Product\_ProductPlant\Actions\FillPlantImages\FillPlantImagesAction;
use App\Modules\Product\_ProductPlant\Actions\FillPlantImages\FillPlantImagesValidation;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schedule;

/*
| Clôture automatique : ouvre les sessions, adjuge / clôture les lots expirés (après soft close).
*/
Artisan::command('auctions:close', function (CloseExpiredAuctionsValidation $validation, CloseExpiredAuctionsAction $action) {
    $result = $action->execute($validation->validate());
    $this->info(json_encode($result));
})->purpose('Clôture les enchères dont l\'heure de clôture effective est dépassée');

Schedule::command('auctions:close')->everyMinute()->withoutOverlapping();

/*
| Collection des plantes : complète l'image des plantes avec une photo d'un produit lié (photo 1 en priorité).
| --all : remplace aussi les images existantes · --dry-run : simulation, n'écrit rien.
*/
Artisan::command('plants:fill-images {--all : Remplacer aussi les images existantes} {--dry-run : Simulation, ne copie rien}', function (FillPlantImagesValidation $validation, FillPlantImagesAction $action) {
    $report = $action->execute($validation->validate(['all' => $this->option('all'), 'dry_run' => $this->option('dry-run')]));

    $labels = ['copiee' => 'copiée', 'a_copier' => 'à copier (simulation)', 'sans_photo' => 'aucune photo de produit', 'fichier_absent' => 'fichier source introuvable'];
    $this->table(['Plante', 'Résultat', 'Photo source'], array_map(fn ($r) => [$r['plante'], $labels[$r['statut']], $r['source'] ?? '—'], $report));

    $counts = array_count_values(array_column($report, 'statut'));
    $this->info(sprintf('%d plante(s) traitée(s) · %d copiée(s) · %d sans photo.', count($report), ($counts['copiee'] ?? 0) + ($counts['a_copier'] ?? 0), ($counts['sans_photo'] ?? 0) + ($counts['fichier_absent'] ?? 0)));
})->purpose('Copie une photo de produit vers l\'image des plantes de la collection qui n\'en ont pas');

/*
| Remet les lots de la session précédente dans la session actuelle, comme nouveaux lots (nouveau numéro,
| enchères remises à zéro, photos copiées). Par défaut : seulement les invendus.
*/
Artisan::command('auctions:relist
    {--from= : Id de la session source (défaut : la session précédente qui contient des lots)}
    {--to= : Id de la session cible (défaut : la session actuelle)}
    {--all : Tous les lots de la session source, pas seulement les invendus}
    {--draft : Créer les nouveaux lots en brouillon au lieu de « En vente »}
    {--dry-run : Simulation, ne crée rien}', function (RelistProductsValidation $validation, RelistProductsAction $action) {
    try {
        $result = $action->execute($validation->validate([
            'from' => $this->option('from'),
            'to' => $this->option('to'),
            'all' => $this->option('all'),
            'draft' => $this->option('draft'),
            'dry_run' => $this->option('dry-run'),
        ]));
    } catch (BusinessRuleException|ValidationException $e) {
        $this->error($e->getMessage());

        return 1;
    }

    $this->line("Session source : <info>{$result['from']}</info> → session cible : <info>{$result['to']}</info>".($this->option('dry-run') ? ' <comment>(simulation)</comment>' : ''));

    $labels = ['cree' => 'créé', 'a_creer' => 'à créer', 'deja_present' => 'déjà dans la session cible'];
    $this->table(['Ancien lot', 'Plante', 'Statut', 'Nouveau lot', 'Résultat', 'Médias'], array_map(
        fn ($l) => [$l['ancien'], $l['nom'], $l['statut'], $l['nouveau'] ?? '—', $labels[$l['resultat']], $l['medias']],
        $result['lots'],
    ));

    $created = count(array_filter($result['lots'], fn ($l) => $l['resultat'] !== 'deja_present'));
    $this->info($result['lots'] === [] ? 'Aucun lot à remettre en vente.' : "{$created} lot(s) ".($this->option('dry-run') ? 'à créer' : 'créé(s)').'.');
})->purpose('Remet les lots de la session précédente dans la session actuelle, comme nouveaux lots');
