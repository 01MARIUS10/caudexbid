<?php

namespace App\Modules\Product\_ProductPlant\Repository;

use App\Modules\Product\_ProductPlant\Models\ProductPlant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductPlantRepository
{
    public function find(int $id): ?ProductPlant
    {
        return ProductPlant::find($id);
    }

    public function create(array $attributes): ProductPlant
    {
        return ProductPlant::create($attributes);
    }

    /** Collection filtrée (recherche nom / genre, genre), avec le nombre de produits liés. */
    public function search(?string $q = null, ?string $genre = null): Collection
    {
        return ProductPlant::withCount('products')
            ->when($genre, fn ($b) => $b->where('genre', $genre))
            ->when($q, fn ($b) => $b->where(fn ($w) => $w
                ->where('nom', 'like', "%{$q}%")
                ->orWhere('genre', 'like', "%{$q}%")))
            ->orderBy('nom')
            ->get();
    }

    /** Toutes les plantes, pour la sélection du formulaire produit. */
    public function all(): Collection
    {
        return ProductPlant::orderBy('nom')->get();
    }

    /** @return list<string> genres utilisés (filtre de la liste) */
    public function genres(): array
    {
        return ProductPlant::whereNotNull('genre')->distinct()->orderBy('genre')->pluck('genre')->all();
    }

    /** Plantes à compléter par la commande plants:fill-images (sans image, ou toutes avec $all). */
    public function forImageFill(bool $all): Collection
    {
        return ProductPlant::when(! $all, fn ($b) => $b->whereNull('image'))->orderBy('id')->get();
    }

    /** Enregistre une image envoyée (remplace et supprime l'ancienne). */
    public function storeImage(ProductPlant $plant, UploadedFile $file): ProductPlant
    {
        return $this->replaceImage($plant, $file->store($this->dir($plant), $this->disk()));
    }

    /**
     * Copie un fichier déjà présent sur le disque des médias (ex. photo d'un produit) comme image de la plante.
     * Copie et non référence : les médias d'un produit sont supprimés au règlement.
     */
    public function copyImageFrom(ProductPlant $plant, string $sourcePath): ProductPlant
    {
        $target = $this->dir($plant).'/'.Str::random(40).'.'.pathinfo($sourcePath, PATHINFO_EXTENSION);
        Storage::disk($this->disk())->copy($sourcePath, $target);

        return $this->replaceImage($plant, $target);
    }

    private function replaceImage(ProductPlant $plant, string $path): ProductPlant
    {
        $old = $plant->image;
        $plant->forceFill(['image' => $path])->save();
        if ($old && $old !== $path) {
            Storage::disk($this->disk())->delete($old);
        }

        return $plant;
    }

    private function dir(ProductPlant $plant): string
    {
        return 'plants/'.$plant->id;
    }

    private function disk(): string
    {
        return config('caudexbid.media.disk');
    }
}
