<?php

namespace App\Repositories;

use App\Models\ProfileImage;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProfileImageRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Gallery rows per profile and transactional reorder for “primary” photo selection.
 */
class ProfileImageRepository extends BaseRepository implements ProfileImageRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param ProfileImage $model Profile image Eloquent model.
     */
    public function __construct(ProfileImage $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    /**
     * Moves `$profileImageId` to display order `1`, toggles `is_display`, and shifts sibling ordering inside a transaction.
     */
    public function setDisplayImage(int $userProfileId, int $profileImageId): void
    {
        DB::transaction(function () use ($userProfileId, $profileImageId) {
            $allImages = $this->model
                ->where('user_profile_id', $userProfileId)
                ->orderBy('display_order')
                ->get();

            if ($allImages->isEmpty()) {
                return;
            }

            $targetImage = $allImages->firstWhere('id', $profileImageId);
            if (!$targetImage) {
                return;
            }

            $reorderedImages = $allImages->reject(function ($image) use ($profileImageId) {
                return $image->id === $profileImageId;
            })->prepend($targetImage);

            foreach ($reorderedImages as $index => $image) {
                $image->update([
                    'display_order' => $index + 1,
                    'is_display' => $index === 0
                ]);
            }
        });
    }
}
