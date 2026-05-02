<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\Language;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Services\LanguageService;
use Illuminate\Support\Collection;

class LanguageServiceUnitTest extends TestCase
{
    public function testGetListOfLanguages()
    {
        $languageMock = Mockery::mock(Language::class)->makePartial();
        $languageMock->id = 1;
        $languageMock->language_name = 'English';

        $languageCollection = collect([$languageMock]);

        $languageRepoMock = $this->mock(LanguageRepositoryInterface::class,
            function($mock) use($languageCollection) {
                $mock->shouldReceive('all')
                ->withNoArgs()
                ->andReturn($languageCollection);
            });
        
        $service = new LanguageService($languageRepoMock);

        $result = $service->all();

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Language::class, $result->get(0));
        $this->assertEquals(1, $result->count());
    }
}
