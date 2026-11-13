<?php

namespace App\Modules\User\_UserGrad\Models;

use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Database\Eloquent\Model;

/**
 * Référentiel des grades : general (0), regulier (3), premium (5), admin (8).
 *
 * @property int $id
 * @property string $code
 * @property string $nom
 * @property int $niveau
 * @property bool $peut_encherir
 * @property bool $peut_changer_grade
 */
class Grade extends Model
{
    protected $table = 'grades';

    protected $fillable = ['code', 'nom', 'niveau', 'peut_encherir', 'peut_changer_grade'];

    protected function casts(): array
    {
        return [
            'niveau' => 'integer',
            'peut_encherir' => 'boolean',
            'peut_changer_grade' => 'boolean',
        ];
    }

    public function codeEnum(): GradeCode
    {
        return GradeCode::from($this->code);
    }
}
