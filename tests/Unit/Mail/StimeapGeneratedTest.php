<?php

namespace Tests\Unit;

use App\Mail\SitemapGenerated;
use App\Services\MusementService;
use App\Services\XMLWriterService;
use Tests\TestCase;

class StimeapGeneratedTest extends TestCase
{
    /**
     * @test
     */
    public function mailable_is_instantiated_and_build()
    {
        $mail = new SitemapGenerated(MusementService::ITALIAN_LOCALE, ["test@test.com"]);
        $xml = $this->app->make(XMLWriterService::class);

        $mail->build($xml);
        $this->assertContains("test@test.com", collect($mail->to)->flatten());
    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}
