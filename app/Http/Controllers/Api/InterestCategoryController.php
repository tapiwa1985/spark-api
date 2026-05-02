<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InterestCategoryResourceCollection;
use App\Services\Contracts\InterestCategoryServiceInterface;

/**
 * Lists interest categories and their interests for onboarding or preference selection UIs.
 *
 * @package App\Http\Controllers\Api
 */
class InterestCategoryController extends Controller
{
    /**
     * Provides hierarchical interest data (categories with child interests).
     */
    private InterestCategoryServiceInterface $_service;

    /**
     * @param InterestCategoryServiceInterface $service Category listing backed by {@see \App\Repositories\InterestCategoryRepository}.
     */
    public function __construct(InterestCategoryServiceInterface $service)
    {
        $this->_service = $service;
    }

    /**
     * @return InterestCategoryResourceCollection Categories with nested interest payloads.
     */
    public function index(): InterestCategoryResourceCollection
    {
        $interestCategories = $this->_service->all();

        return new InterestCategoryResourceCollection($interestCategories);
    }
}
