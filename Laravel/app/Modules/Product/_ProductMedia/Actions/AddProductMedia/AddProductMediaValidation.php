<?php

namespace App\Modules\Product\_ProductMedia\Actions\AddProductMedia;

use App\Modules\Product\_ProductMedia\Models\Enums\MediaType;
use App\Modules\Product\_ProductMedia\Models\Enums\PhotoView;
use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Contenu multimédia : 4 photos max (1 face, 2 côtés, 1 arrière) et 1 vidéo d'environ 15 s.
 */
final class AddProductMediaValidation
{
    public function rules(array $data): array
    {
        $isVideo = ($data['type'] ?? null) === MediaType::Video->value;

        return [
            'produit_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', Rule::enum(MediaType::class)],
            'position' => [$isVideo ? 'nullable' : 'required', 'integer', 'between:1,'.config('caudexbid.media.max_photos')],
            'vue' => [$isVideo ? 'prohibited' : 'required', Rule::enum(PhotoView::class)],
            'fichier' => $isVideo
                ? ['required', 'file', 'mimes:mp4,mov,webm', 'max:51200']
                : ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'duree_secondes' => [$isVideo ? 'required' : 'prohibited', 'integer', 'between:1,'.config('caudexbid.media.max_video_seconds')],
            'est_vitrine' => ['nullable', 'boolean'],
        ];
    }

    public function validate(array $data, UserAuth $actor): AddProductMediaInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return AddProductMediaInput::fromArray(Validator::make($data, $this->rules($data))->validate());
    }
}
