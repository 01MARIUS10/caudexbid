<?php

namespace App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia;

use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;

final class DeleteProductMediaAction
{
    public function __construct(private readonly ProductMediaRepository $media) {}

    public function execute(DeleteProductMediaInput $input): void
    {
        $this->media->delete($this->media->find($input->mediaId));
    }
}
