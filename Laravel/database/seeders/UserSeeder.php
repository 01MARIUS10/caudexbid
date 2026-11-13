<?php

namespace Database\Seeders;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Grade;
use App\Modules\User\_UserGrad\Models\UserGrade;
use App\Modules\User\_UserProfil\Models\UserPreference;
use App\Modules\User\_UserProfil\Models\UserProfile;
use Illuminate\Database\Seeder;

/**
 * Membres de démonstration (mot de passe : « password »).
 *
 * admin (A) · andy88, mika_p (P) · kenji, lina, minho (M) · chen, hiro, sora (G)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            // identifiant, grade, parrain, nom, nationalite, pays
            ['admin', 'A', null, 'Administrateur caudexbid', 'korea', 'Corée'],
            ['andy88', 'P', null, 'Andy Kim', 'korea', 'Corée'],
            ['mika_p', 'P', null, 'Mika Tanaka', 'japan', 'Japon'],
            ['kenji', 'M', 'mika_p', 'Kenji Sato', 'japan', 'Japon'],
            ['lina', 'M', 'andy88', 'Lina Park', 'korea', 'Corée'],
            ['minho', 'M', 'andy88', 'Minho Lee', 'korea', 'Corée'],
            ['chen', 'G', null, 'Chen Wei', 'china', 'Chine'],
            ['hiro', 'G', null, 'Hiro Yamada', 'japan', 'Japon'],
            ['sora', 'G', 'mika_p', 'Sora Ito', 'japan', 'Japon'],
        ];

        $grades = Grade::pluck('id', 'code');
        $admin = null;

        foreach ($members as [$identifiant, $grade, $parrain, $nom, $nationalite, $pays]) {
            $user = UserAuth::create([
                'identifiant' => $identifiant,
                'email' => $identifiant.'@caudexbid.test',
                'password' => 'password',
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $admin ??= $user;

            UserProfile::create([
                'membre_id' => $user->id,
                'nom' => $nom,
                'telephone' => '+82 10 0000 '.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'adresse' => $user->id.' Demo street, '.$pays,
                'pays' => $pays,
                'nationalite' => $nationalite,
                'parrain_id' => $parrain ? UserAuth::where('identifiant', $parrain)->value('id') : null,
            ]);

            UserPreference::create(['membre_id' => $user->id]);

            // Historique : inscription en G, puis promotion éventuelle.
            UserGrade::create(['user_id' => $user->id, 'grade_id' => $grades['G'], 'edited_by_user_id' => null]);
            if ($grade !== 'G') {
                $editor = match ($grade) {
                    'M' => UserAuth::where('identifiant', $parrain)->value('id'),
                    default => $grade === 'A' ? null : $admin->id,
                };
                UserGrade::create(['user_id' => $user->id, 'grade_id' => $grades[$grade], 'edited_by_user_id' => $editor]);
            }
        }
    }
}
