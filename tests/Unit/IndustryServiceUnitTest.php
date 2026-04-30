<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use App\Models\Industry;
use App\Repositories\Contracts\IndustryRepositoryInterface;
use Illuminate\Support\Collection;
use App\Services\IndustryService;

class IndustryServiceUnitTest extends TestCase
{
    public function testGetListOfIndustries()
    {
        $industryMock = Mockery::mock(Industry::class)->makePartial();
        $industryMock->id = 1;
        $industryMock->industry_name = 'FINANCE';

        $industryCollection = collect([$industryMock]);

        $indstryRepoMock = $this->mock(IndustryRepositoryInterface::class,
            function($mock) use($industryCollection) {
                $mock->shouldReceive('all')
                ->withNoArgs()
                ->andReturn($industryCollection);
            });
        
        $service = new IndustryService($indstryRepoMock);

        $result = $service->all();

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Industry::class, $result->get(0));
        $this->assertEquals(1, $result->count());
    }
}
