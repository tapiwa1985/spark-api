<?php

namespace App\Repositories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\LanguageRepositoryInterface;

/**
 * CRUD/list access for {@see Language} records.
 */
class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    protected Model $model;

    /**
     * @param Language $model Language root model.
     */
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }
}
