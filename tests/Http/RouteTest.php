<?php

namespace Canvas\Tests\Http;

use Canvas\Tests\TestCase;

class RouteTest extends TestCase
{
    /** @test */
    public function it_has_named_routes()
    {
        $this->assertEquals(
            url(config('canvas.path')),
            route('canvas')
        );
    }
}
