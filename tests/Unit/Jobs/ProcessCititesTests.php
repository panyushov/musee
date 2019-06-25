<?php

namespace Tests\Unit;

use App\Jobs\ProcessActivities;
use App\Jobs\ProcessCities;
use App\Services\MusementService;
use App\Services\XMLWriterService;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessCititesTests extends TestCase
{
    /**
     * @test
     */
    public function process_activities_pushed_successfully()
    {
        Queue::fake();

        ProcessCities::dispatch(MusementService::ITALIAN_LOCALE);
        Queue::assertPushed(ProcessCities::class);
    }

    /**
     * @test
     */
    public function process_cities_handled_correctly()
    {
        Queue::fake();

        $xml = $this->mock(XMLWriterService::class);
        $muse = $this->mock(MusementService::class);
        $b = new \stdClass();
        $b->id = "52";
        $b->url = "TestUrl";
        $collection = collect([
            $b, $b
        ]);
        $muse->shouldReceive('getCities')->once()->andReturn($collection);

        $xml->shouldReceive('initFileLocale')->once()->with(MusementService::ITALIAN_LOCALE);
        $xml->shouldReceive('addCityNode')->with("TestUrl", MusementService::ITALIAN_LOCALE);

        $job = new ProcessCities(MusementService::ITALIAN_LOCALE);
        $job->handle($muse, $xml);

        Queue::shouldReceive(ProcessActivities::class);
    }


    protected function setUp(): void
    {
        parent::setUp();
    }
}
