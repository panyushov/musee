<?php

namespace Tests\Unit;

use App\Http\Middleware\Generating;
use App\Jobs\ProcessCities;
use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class GeneratingMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->qServ = $this->app->make(DatabaseQueueService::class);
    }

    /**ø
     * @test
     */
    public function redirected_from_config_when_queue_not_empty()
    {
        Queue::fake();

        $request = Request::create('/config', 'POST');
        ProcessCities::dispatch(MusementService::ITALIAN_LOCALE);
        $middleware = new Generating($this->qServ);
        $response = $middleware->handle($request, function () {
        });
        $this->assertEquals($response->getStatusCode(), 302);
    }

    /**
     * @test
     */
    public function passed_through_when_queue_empty()
    {
        Queue::fake();

        $request = Request::create('/config', 'POST');
        $middleware = new Generating($this->qServ);
        $response = $middleware->handle($request, function () {
        });
        $this->assertNull($response);
    }

}
