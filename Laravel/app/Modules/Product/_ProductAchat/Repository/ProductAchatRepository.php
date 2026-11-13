<?php

namespace App\Modules\Product\_ProductAchat\Repository;

use App\Modules\Product\_ProductAchat\Models\ProductAchat;

class ProductAchatRepository
{
    public function findByProduit(int $produitId): ?ProductAchat
    {
        return ProductAchat::where('produit_id', $produitId)->first();
    }

    public function findByProduitForUpdate(int $produitId): ?ProductAchat
    {
        return ProductAchat::where('produit_id', $produitId)->lockForUpdate()->first();
    }

    public function definePrice(int $produitId, string $prix): ProductAchat
    {
        return ProductAchat::updateOrCreate(['produit_id' => $produitId], ['prix' => $prix]);
    }

    public function markBought(ProductAchat $achat, int $acheteurId): ProductAchat
    {
        $achat->forceFill(['acheteur_id' => $acheteurId, 'achete_le' => now()])->save();

        return $achat;
    }
}
