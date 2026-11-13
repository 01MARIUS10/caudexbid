<?php

namespace Database\Seeders;

use App\Modules\Product\Models\ProductOwner;
use Illuminate\Database\Seeder;

class ProductOwnerSeeder extends Seeder
{
    public function run(): void
    {
        ProductOwner::create([
            'nom' => 'Pépinière Ankarana',
            'pays' => 'Madagascar',
            'email' => 'ankarana@caudexbid.test',
            'infos_bancaires' => 'BOA Madagascar — IBAN MG46 0000 0000 0000 0000 0000 001',
        ]);

        ProductOwner::create([
            'nom' => 'Isalo Caudex Farm',
            'pays' => 'Madagascar',
            'email' => 'isalo@caudexbid.test',
            'infos_bancaires' => 'BNI Madagascar — IBAN MG46 0000 0000 0000 0000 0000 002',
        ]);
    }
}
