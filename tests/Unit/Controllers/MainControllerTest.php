<?php

namespace Tests\Unit;

use App\Http\Controllers\MainController;
use App\Models\SMTPConfig;

use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery\Mock;
use Tests\TestCase;

class MainControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var MainController $mainController */
    protected $mainController;

    /**
     * @test
     */
    public function config_request_processed_successfully()
    {
        $input = [
            'host' => 'test.host',
            'port' => '20',
            'username' => 'username',
            'password' => 'password',
            'from' => 'from@test.com',
        ];
        $request = new Request($input);
        $this->mainController->processConfig($request);

        $this->assertEquals($input['host'], SMTPConfig::config()->host);
        $this->assertEquals(SMTPConfig::count(), 1);
    }

    /**
     * @test
     */
    public function validation_exception_is_thrown()
    {
        $this->expectException(ValidationException::class);

        $input = [
            'host' => 'test.host',
            'port' => '5645646',
            'username' => 'username',
            'password' => 'password',
            'from' => 'from@test.com',
        ];
        $request = new Request($input);
        $this->mainController->processConfig($request);
    }

    /**
     * @test
     */
    public function generation_started()
    {
        /** @var Mock $qServ */
        $qServ = $this->mock(DatabaseQueueService::class);
        $qServ->shouldReceive("dispatchCitiesJobs")->once();

        $input = [
            'emails' => 'test@test.com',
            'locale' => MusementService::ITALIAN_LOCALE,
        ];
        $request = new Request($input);

        $this->mainController->startGeneration($request, $qServ);
    }

    /**
     * @test
     */
    public function generation_not_started_due_to_emails_validation()
    {
        $this->expectException(ValidationException::class);
        /** @var Mock $qServ */
        $qServ = $this->mock(DatabaseQueueService::class);
        $qServ->shouldNotReceive("dispatchCitiesJobs");

        $input = [
            'emails' => 'a,b,c,d',
        ];
        $request = new Request($input);

        $this->mainController->startGeneration($request, $qServ);
    }

    /**
     * @test
     */
    public function generator_page_rendered()
    {
        /** @var Mock $qServ */
        $qServ = $this->mock(DatabaseQueueService::class);
        $qServ->shouldReceive("generationInProgress")->once();

        $this->mainController->generator($qServ);
    }

    /**
     * @test
     */
    public function config_page_rendered()
    {
        /** @var Mock $qServ */
        $qServ = $this->mock(DatabaseQueueService::class);
        $qServ->shouldReceive("generationInProgress")->once();

        $this->mainController->config($qServ);
    }

    /**
     * @test
     */
    public function index_redirect_with_config()
    {
        factory(SMTPConfig::class)->make()->save();
        $response = $this->mainController->index();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('musee.generator'), $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function index_redirect_without_config()
    {
        $response = $this->mainController->index();
        $this->assertEquals(route('musee.config'), $response->getTargetUrl());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mainController = $this->app->make(MainController::class);
    }


}
