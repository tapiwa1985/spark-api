<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\IndustryRepositoryInterface;
use App\Models\Industry;
use Illuminate\Support\Collection;

class IndustryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private IndustryRepositoryInterface $_industryRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_industryRepository = app()->make(IndustryRepositoryInterface::class);
    }

    public function testGetListOfIndustries()
    {
        Industry::factory(10)->create();

        $result = $this->_industryRepository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(10, $result->count());
        $this->assertInstanceOf(Industry::class, $result->get(0));
    }
}
