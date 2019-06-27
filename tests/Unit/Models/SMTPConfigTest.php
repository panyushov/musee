<?php

namespace Tests\Unit;

use App\Models\SMTPConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SMTPConfigTest extends TestCase
{

    use RefreshDatabase;
    protected $hostNameTest = "host-test";
    protected $testRecipients = "recipient1, recipient2, recipient3";
    protected $testCorruptMessage = "Test Corrupt Message";

    /**
     * @test
     */
    public function new_config_successfully_saved()
    {
        SMTPConfig::truncate();
        $this->assertNull(SMTPConfig::first());

        factory(SMTPConfig::class)->make()->save();
        $this->assertEquals(SMTPConfig::first()->count(), 1);

        $input = [
            "host" => "host-test",
            "port" => "25",
            "username" => "username-test",
            "password" => "password-test",
            "encryption" => "encryption-test",
            "from" => "from-test",
            "notify" => "notify-test",
            "corrupt" => "corrucpt-test",
        ];
        SMTPConfig::saveConfig($input);
        $this->assertEquals(SMTPConfig::first()->count(), 1);

        $this->assertEquals(SMTPConfig::first()->host, $this->hostNameTest);
    }

    /**
     * @test
     */
    public function recipients_successfully_saved()
    {
        SMTPConfig::truncate();
        $this->assertEquals(SMTPConfig::count(), 0);
        SMTPConfig::setRecipients($this->testRecipients);

        factory(SMTPConfig::class)->make()->save();
        $this->assertEquals(SMTPConfig::count(), 1);
        SMTPConfig::setRecipients($this->testRecipients);
        $this->assertEquals($this->testRecipients, SMTPConfig::first()->notify);
    }

    /**
     * @test
     */
    public function config_successfully_retrieved()
    {
        SMTPConfig::truncate();
        $this->assertEquals(SMTPConfig::count(), 0);
        factory(SMTPConfig::class)->make()->save();
        $this->assertEquals(1, SMTPConfig::config()->count());
    }

    /**
     * @test
     */
    public function config_exists_or_does_not()
    {
        SMTPConfig::truncate();
        $this->assertEquals(SMTPConfig::count(), 0);
        $this->assertEquals(false, SMTPConfig::exists());
        factory(SMTPConfig::class)->make()->save();
        $this->assertEquals(true, SMTPConfig::exists());
    }

    /**
     * @test
     */
    public function config_successfully_marked_as_corrupt()
    {
        SMTPConfig::truncate();
        $this->assertEquals(SMTPConfig::count(), 0);
        SMTPConfig::markAsCorrupt($this->testCorruptMessage);

        $testConfig = factory(SMTPConfig::class)->make();
        $testConfig->corrupt = $this->testCorruptMessage . "diff";
        $testConfig->save();
        SMTPConfig::markAsCorrupt($this->testCorruptMessage);
        $this->assertNotEquals($this->testCorruptMessage, SMTPConfig::first()->corrupt);

        SMTPConfig::truncate();

        factory(SMTPConfig::class)->state('no-corrupt')->make()->save();
        SMTPConfig::markAsCorrupt($this->testCorruptMessage);
        $this->assertEquals($this->testCorruptMessage, SMTPConfig::first()->corrupt);
    }

    /**
     * @test
     */
    public function set_app_config_from_model()
    {
        factory(SMTPConfig::class)->make()->save();
        SMTPConfig::first()->setConfig();
        $this->assertEquals(Config::get('mail.host'), SMTPConfig::first()->host);
        $this->assertEquals(Config::get('mail.port'), SMTPConfig::first()->port);
        $this->assertEquals(Config::get('mail.from.address'), SMTPConfig::first()->from);
    }

    /**
     * @test
     */
    public function retrieve_recipients()
    {
        factory(SMTPConfig::class)->make()->save();
        $this->assertIsArray(SMTPConfig::config()->getRecipients());
        $this->assertEmpty(SMTPConfig::config()->getRecipients());

        SMTPConfig::truncate();
        factory(SMTPConfig::class)->state('with-recipients-and-locale')->make()->save();
        $this->assertIsArray(SMTPConfig::config()->getRecipients());
        $this->assertNotEmpty(SMTPConfig::config()->getRecipients());
    }

    public function setUp(): void
    {
        parent::setUp();

    }
}
