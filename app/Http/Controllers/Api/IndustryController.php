<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\IndustryServiceInterface;
use App\Http\Resources\IndustryResourceCollection;

class IndustryController extends Controller
{
    /**
     * @var IndustryServiceInterface
     */
    private IndustryServiceInterface $_industryService;

    /**
     * IndustryController constructor.
     *
     * @param IndustryServiceInterface
     */
    public function __construct(IndustryServiceInterface $industryService)
    {
        $this->_industryService = $industryService;
    }

    /**
     * Returns a collection of industries.
     *
     * @return IndustryResourceCollection
     */
    public function index(): IndustryResourceCollection
    {
        $industries = $this->_industryService->all();

        return new IndustryResourceCollection($industries);
    }
}
