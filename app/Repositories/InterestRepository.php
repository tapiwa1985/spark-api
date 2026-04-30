<?php 

namespace App\Repositories;

use App\Models\Interest;
use App\Repositories\Contracts\InterestRepositoryInterface;

class InterestRepository extends BaseRepository implements InterestRepositoryInterface 
{
    /**
     * @var Interest
     */
    private Interest $model;

    /**
     * InterestRepository constructor.
     *  
     * @param Interest
     */
    public function __construct(Interest $model)
    {
        parent::__construct($model);
    }
}