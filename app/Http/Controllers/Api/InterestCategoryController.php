<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InterestCategoryResourceCollection;
use App\Services\Contracts\InterestCategoryServiceInterface;

class InterestCategoryController extends Controller
{
    /**
     *  @var InterestCategoryServiceInterface
     * */
    private InterestCategoryServiceInterface $_service;

    /**
     * InterestCategoryController constructor.
     *
     * @param InterestCategoryServiceInterface $service
     */
    public function __construct(InterestCategoryServiceInterface $service)
    {
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return InterestCategoryResourceCollection
     */
    public function index(): InterestCategoryResourceCollection
    {
        $interestCategories = $this->_service->all();

        return new InterestCategoryResourceCollection($interestCategories);
    }
}
