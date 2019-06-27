<?php

namespace Tests\Unit;

use App\Jobs\ProcessCities;
use App\Mail\SitemapGenerated;
use App\Models\SMTPConfig;
use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DatabaseQueueServiceTest extends TestCase
{

    use RefreshDatabase;

    protected $testLocale;
    /** @var Mock $configMock */
    protected $configMock;
    /** @var DatabaseQueueService $db */
    private $db = null;

    /**
     * @test
     */
    public function dispatches_cities_jobs_correctly()
    {
        Queue::fake();
        $this->db->dispatchCitiesJobs();

        Queue::assertPushed(ProcessCities::class, 1);
    }

    /**
     * @test
     */
    public function correctly_tells_generation_in_progress()
    {
        Queue::fake();
        ProcessCities::dispatch($this->testLocale);
        $this->assertEquals(1, Queue::size());
        $this->assertTrue($this->db->generationInProgress());
    }

    /**
     * @test
     */
    public function correctly_tells_no_generation_in_progress()
    {
        Queue::fake();
        $this->assertFalse($this->db->generationInProgress());
    }

    /**
     * @test
     */
    public function queue_completion_not_processed_when_generating()
    {
        Queue::fake();
        Mail::fake();
        ProcessCities::dispatch($this->testLocale);
        /** @var Mock $configMock */
        $configMock = Mockery::mock('alias:App\Models\SMTPConfig');
        $configMock->shouldNotReceive("exists");

        $this->db->queueIsComplete();
    }

    /**
     * @test
     */
    public function queue_completion_proceeds_and_stops_no_recipients()
    {
        Mockery::close();
        Mail::fake();
        // Config without recipients
        factory(SMTPConfig::class)->make()->save();

        $this->db->queueIsComplete();

        Mail::assertNothingSent();
    }

    /**
     * @test
     */
    public function queue_completion_proceeds_till_mailing()
    {
        Mail::fake();
        // Config with recipients
        factory(SMTPConfig::class)->state('with-recipients-and-locale')->make()->save();

        $this->db->queueIsComplete();

        Mail::assertSent(SitemapGenerated::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close();
        $this->testLocale = MusementService::ITALIAN_LOCALE;
        $this->db = $this->app->make(DatabaseQueueService::class);
    }
}
