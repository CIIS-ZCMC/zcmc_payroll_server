<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Refuse to run against anything but an in-memory sqlite database.
     *
     * phpunit.xml sets the connection, but a cached config
     * (bootstrap/cache/config.php, written by config:cache or optimize) takes
     * precedence over it. When that happened here the suite silently retargeted
     * the real MySQL database and RefreshDatabase ran migrate:fresh against it,
     * dropping every table.
     *
     * A cheap assertion is worth more than the convention it enforces.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            $this->fail(sprintf(
                "Tests are pointed at the '%s' connection (database: %s), not in-memory sqlite. "
                . "This is almost always a stale config cache overriding phpunit.xml. "
                . "Run: php artisan config:clear",
                $connection,
                var_export($database, true)
            ));
        }

        DB::connection()->getPdo();
    }
}
