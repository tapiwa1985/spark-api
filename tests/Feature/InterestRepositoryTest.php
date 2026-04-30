<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Interest;
use App\Models\InterestCategory;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\InterestRepositoryInterface;

class InterestRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var InterestRepositoryInterface
     */
    private InterestRepositoryInterface $_interestRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_interestRepository = app()->make(InterestRepositoryInterface::class);
    }

    public function testGetInterestsByCategory()
    {
        $interestCategory = InterestCategory::factory()->create();

        $interests = Interest::factory(5)->create([
            'interest_category_id' => $interestCategory->id,
        ]);

        $result = $this->_interestRepository->findByCategoryId($interestCategory->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(5, $result->count());
        $this->assertInstanceOf(Interest::class, $result->get(0));
    }
}
