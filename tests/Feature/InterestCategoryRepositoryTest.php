<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\InterestCategory;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\InterestCategoryRepositoryInterface;

class InterestCategoryRepositoryTest extends TestCase
{
    /**
     * @var InterestCategoryRepositoryInterface
     */
    private InterestCategoryRepositoryInterface $_interestCategoryRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_interestCategoryRepository = app()->make(InterestCategoryRepositoryInterface::class);
    }

    public function testGetAllInterestCategories(): void
    {
        $categories = InterestCategory::factory(3)->create();

        $result = $this->_interestCategoryRepository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(3, $result->count());
        $this->assertInstanceOf(InterestCategory::class, $result->get(0));
    }
}
