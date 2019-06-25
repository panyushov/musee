<?php

namespace Tests\Unit;

use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

class MusementServiceTest extends TestCase
{
    protected $testLocale;
    /** @var Mock $configMock */
    protected $configMock;
    /** @var MusementService $muSe */
    protected $muSe;
    protected $testActivityTitle = "Test Activity";
    protected $testCityID = 57;
    protected $testActivityURL = "https://test.com/activity";
    protected $testCityName = "Amsterdam";
    protected $testCityURL = "https://test.com/city";
    /** @var DatabaseQueueService $db */
    private $db = null;


    /**
     * @test
     */
    public function all_supported_locales_returned_correctly()
    {
        $this->assertIsArray(MusementService::allSupportedLocales());
    }

    /**
     * @test
     */
    public function correctly_fetches_cities_from_api()
    {
        /** @var Mock $mockedClient */
        $mockedClient = $this->mock(Client::class);
        $this->app->instance(Client::class, $mockedClient);
        $response = $this->getCitiesResponse();
        $mockedClient->shouldReceive("request")->once()->andReturn(($response));

        $mockedMuSe = $this->app->make(MusementService::class);

        $first = $mockedMuSe->getCities()->first();
        $this->assertEquals($first->url, $this->testCityURL);
        $this->assertEquals($first->name, $this->testCityName);
    }

    /**
     * @return Response
     */
    public function getCitiesResponse(): Response
    {
        $response = new Response(200, [], $this->sampleCitiesJSON());
        return $response;
    }

    private function sampleCitiesJSON()
    {
        return /** @lang JSON */
            <<<CITY_SAMPLE
            [  
               {  
                  "id":$this->testCityID,
                  "name":"$this->testCityName",
                  "cover_image_url":"https:\/\/images.musement.com\/cover\/0002\/15\/amsterdam_header-114429.jpeg",
                  "url":"$this->testCityURL"
               }
            ]
CITY_SAMPLE;
    }

    /**
     * @test
     */
    public function correctly_fetches_activities_from_ali()
    {
        /** @var Mock $mockedClient */
        $mockedClient = $this->mock(Client::class);
        $this->app->instance(Client::class, $mockedClient);
        $response = $this->getActivityResponse();
        $mockedClient->shouldReceive("request")->once()->andReturn($response);

        /** @var MusementService $mockedMuSe */
        $mockedMuSe = $this->app->make(MusementService::class);

        $first = $mockedMuSe->getActivities($this->testCityID)->first();
        $this->assertEquals($first->url, $this->testActivityURL);
        $this->assertEquals($first->title, $this->testActivityTitle);
    }

    private function getActivityResponse()
    {
        $response = new Response(200, [], $this->sampleActivitesJSON());
        return $response;
    }

    private function sampleActivitesJSON()
    {
        return /** @lang JSON */
            <<<ACTIVITY_SAMPLE
            {
              "meta": {
                "count": 146,
                "match_type": "cities",
                "match_names": [
                  "Amsterdam"
                ],
                "match_ids": [
                  "57"
                ]
              },
              "data": [
                {
                  "title": "$this->testActivityTitle",
                  "url": "$this->testActivityURL"
                }
              ]
            }
ACTIVITY_SAMPLE;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close();
        $this->testLocale = MusementService::ITALIAN_LOCALE;
    }
}
