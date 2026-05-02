<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\IndustryServiceInterface;
use App\Http\Resources\IndustryResourceCollection;

/**
 * Exposes the industry taxonomy for profile job context (dropdowns, filters).
 */
class IndustryController extends Controller
{
    /**
     * Loads {@see \App\Models\Industry} rows through the service layer.
     */
    private IndustryServiceInterface $_industryService;

    /**
     * @param IndustryServiceInterface $industryService Repository-backed listing service.
     */
    public function __construct(IndustryServiceInterface $industryService)
    {
        $this->_industryService = $industryService;
    }

    /**
     * @return IndustryResourceCollection Every industry row ordered by repository defaults.
     */
    public function index(): IndustryResourceCollection
    {
        $industries = $this->_industryService->all();

        return new IndustryResourceCollection($industries);
    }
}
