<?php

namespace Tests\Feature;

use Tests\TestCase;

class BasicRouteTest extends TestCase
{
    /**
     * Testing sitemap generator route.
     *
     * @return void
     */
    public function testGeneratorRoute()
    {
        $response = $this->get('/generator');

        $response->assertStatus(200);
    }

    /**
     * Testing config.
     *
     * @return void
     */
    public function testConfigRoute()
    {
        $response = $this->get('/config');

        $response->assertStatus(200);
    }

    /**
     * Testing sitemap generator route.
     *
     * @return void
     */
    public function testRoot()
    {
        $response = $this->get('/');
        // Should redirect
        $response->assertStatus(302);
    }
}
