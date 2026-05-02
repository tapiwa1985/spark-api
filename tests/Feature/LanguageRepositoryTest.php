<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Language;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\LanguageRepositoryInterface;

class LanguageRepositoryTest extends TestCase
{
    private LanguageRepositoryInterface  $_languageRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_languageRepository = app()->make(LanguageRepositoryInterface::class);
    }

    public function testGetListOfLanguages()
    {
        Language::factory(10)->create();

        $result = $this->_languageRepository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(10, $result->count());
        $this->assertInstanceOf(Language::class, $result->get(0));
    }
}
