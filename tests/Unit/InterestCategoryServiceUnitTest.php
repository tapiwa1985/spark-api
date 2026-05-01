<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\InterestCategory;
use App\Services\Contracts\InterestCategoryServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

/**
 * Class InterestCategoryServiceUnitTest
 *
 * @package Tests\Unit
 */
class InterestCategoryServiceUnitTest extends TestCase
{
    use RefreshDatabase;

    public function testGetListOfInterestCategories(): void
    {
        InterestCategory::factory()->create(['interest_category_name' => 'Hobbies']);
        InterestCategory::factory()->create(['interest_category_name' => 'Sports']);

        $service = app()->make(InterestCategoryServiceInterface::class);
        $result = $service->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame('Hobbies', $result->first()->interest_category_name);
    }
}
