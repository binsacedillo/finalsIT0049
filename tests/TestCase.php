<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Front-end assets are built for local use, but are not required by HTTP tests.
        $this->withoutVite();
    }
}
