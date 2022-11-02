<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Overrides parent::setUp();
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Returns random user from the database or create a new one if there are no users
     *
     * @return User
     */
    public static function getRandomUser(): User
    {
        return User::inRandomOrder()->first() ?? User::factory()->create();
    }

    /**
     * @param [Illuminate\Testing\Fluent\AssertableJson] $json x
     * @param [array] $dataTests
     * @return void
     */
    public static function assertableJsonPaginationTest($json, $dataTests)
    {
        return $json->whereType('current_page', 'integer')
            ->whereType('data', 'array')
            ->whereAllType($dataTests)
            ->whereType('first_page_url', 'string')
            ->whereType('from', 'integer')
            ->whereType('last_page', 'integer')
            ->whereType('last_page_url', 'string')
            ->whereType('links', 'array')
            ->whereType('next_page_url', 'string|null')
            ->whereType('path', 'string')
            ->whereType('per_page', 'integer')
            ->whereType('prev_page_url', 'string|null')
            ->whereType('to', 'integer')
            ->whereType('total', 'integer');
    }
}
