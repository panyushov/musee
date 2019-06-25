<?php

namespace Tests\Unit;

use App\Jobs\ProcessActivities;
use App\Services\MusementService;
use App\Services\XMLWriterService;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessActivitiesTests extends TestCase
{
    /**
     * @test
     */
    public function process_activities_pushed_successfully()
    {
        Queue::fake();

        ProcessActivities::dispatch(52, MusementService::ITALIAN_LOCALE);
        Queue::assertPushed(ProcessActivities::class);
    }

    /**
     * @test
     */
    public function process_activities_handled_correctly()
    {
        $xml = $this->mock(XMLWriterService::class);
        $muse = $this->mock(MusementService::class);
        $b = new \stdClass();
        $b->url = "A";
        $b->locale = MusementService::ITALIAN_LOCALE;
        $collection = collect([
            $b
        ]);
        $muse->shouldReceive('getActivities')->once()->andReturn($collection);
        $xml->shouldReceive('addActivityNode')->once();
        $job = new ProcessActivities(52, MusementService::ITALIAN_LOCALE);
        $job->handle($muse, $xml);
    }


    protected function setUp(): void
    {
        parent::setUp();
    }
}
