<?php

namespace Database\Seeders;

use App\Modules\User\_UserGrad\Models\Grade;
use Illuminate\Database\Seeder;

/**
 * Barème des grades : 0 général, 3 membre régulier, 5 premium, 8 administrateur.
 */
class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['code' => 'G', 'nom' => 'general', 'niveau' => 0, 'peut_encherir' => false, 'peut_changer_grade' => false],
            ['code' => 'M', 'nom' => 'regulier', 'niveau' => 3, 'peut_encherir' => true, 'peut_changer_grade' => false],
            ['code' => 'P', 'nom' => 'premium', 'niveau' => 5, 'peut_encherir' => true, 'peut_changer_grade' => true],
            ['code' => 'A', 'nom' => 'admin', 'niveau' => 8, 'peut_encherir' => false, 'peut_changer_grade' => true],
        ];

        foreach ($grades as $grade) {
            Grade::updateOrCreate(['code' => $grade['code']], $grade);
        }
    }
}
