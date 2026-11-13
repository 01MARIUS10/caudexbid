<?php

namespace Database\Seeders;

use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaAction;
use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaValidation;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Photos (3) et vidéo (1) de chaque plante, téléchargées en local dans database/seeders/media/{nom-de-la-plante}/
 * (photo-1.jpg, photo-2.jpg, photo-3.jpg, video.mp4), puis enregistrées via l'action AddProductMedia.
 * La durée de chaque vidéo est déclarée dans media/videos.php.
 */
class ProductMediaSeeder extends Seeder
{
    /** Vue de chaque photo : 1 = face, 2 = côté, 3 = arrière. */
    private const VIEWS = [1 => 'front', 2 => 'side', 3 => 'back'];

    public function run(AddProductMediaValidation $validation, AddProductMediaAction $action): void
    {
        // Repart d'un dossier propre : migrate:fresh vide la base mais pas les fichiers déjà copiés.
        Storage::disk(config('caudexbid.media.disk'))->deleteDirectory('products');

        $admin = UserAuth::where('identifiant', config('caudexbid.system_sender_identifiant'))->firstOrFail();
        $dir = database_path('seeders/media');
        $durations = require $dir.'/videos.php';

        // Écran d'accueil : photo de face des 8 premiers lots actifs.
        $showcase = Product::where('statut', ProductStatus::Active)->orderBy('id')->limit(8)->pluck('id')->all();

        foreach (Product::orderBy('id')->get() as $product) {
            $slug = Str::slug($product->nom);

            foreach (self::VIEWS as $position => $vue) {
                $file = "{$dir}/{$slug}/photo-{$position}.jpg";
                if (! is_file($file)) {
                    continue;
                }

                $action->execute($validation->validate([
                    'produit_id' => $product->id,
                    'type' => 'photo',
                    'position' => $position,
                    'vue' => $vue,
                    'fichier' => $this->upload($file),
                    'est_vitrine' => $position === 1 && in_array($product->id, $showcase, true),
                ], $admin));
            }

            $video = "{$dir}/{$slug}/video.mp4";
            if (is_file($video) && isset($durations[$slug])) {
                $action->execute($validation->validate([
                    'produit_id' => $product->id,
                    'type' => 'video',
                    'fichier' => $this->upload($video),
                    'duree_secondes' => $durations[$slug],
                ], $admin));
            }
        }
    }

    /** Fichier local présenté comme un envoi (le fichier source n'est ni déplacé ni supprimé). */
    private function upload(string $path): UploadedFile
    {
        return new UploadedFile($path, basename($path), null, null, true);
    }
}
