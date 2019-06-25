<?php

namespace Tests\Unit;

use App\Services\XMLWriterService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class XMLWriteServiceTest extends TestCase
{
    /** @var XMLWriterService $xml */
    protected $xml;
    protected $testLocale = "test-test";
    protected $testURL = "https://test.com/test";

    /**
     * @test
     */
    public function correctly_initialize_xml_file()
    {
        Storage::fake();
        Storage::shouldReceive("delete")->once();
        Storage::shouldReceive("put")->once();

        $this->xml->initFileLocale($this->testLocale);
    }

    /**
     * @test
     */
    public function correctly_returns_locale_file_path_and_file_name()
    {
        $this->assertNotEmpty($this->xml->localeFileName($this->testLocale));
        $this->assertNotEmpty($this->xml->localeFileName($this->testLocale));
    }

    /**
     * @test
     */
    public function correctly_adds_nodes_in_the_file()
    {
        Storage::fake();

        Storage::shouldReceive("get")->twice()->andReturn($this->emptyXML());
        Storage::shouldReceive("put")->twice()->andReturn($this->emptyXML());;

        $this->xml->addCityNode($this->testURL, $this->testLocale);
        $this->xml->addActivityNode($this->testURL, $this->testLocale);
    }

    private function emptyXML()
    {
        return /** @lang XML */
            <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
TEXT;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->xml = $this->app->make(XMLWriterService::class);
    }
}
