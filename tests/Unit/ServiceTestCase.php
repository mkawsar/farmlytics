<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BasePhpUnitTestCase;

/**
 * Base class for unit tests of the service layer.
 * - Initialize the Facade container
 * - Provide an auth mock (set the user via setAuthUserId)
 *
 * TODO: If services are refactored to avoid direct references to Auth and instead
 *       accept userId, etc. as parameters, remove the auth mock initialization (setUp)
 *       and setAuthUserId from this class.
 */
abstract class ServiceTestCase extends BasePhpUnitTestCase
{
    use MockeryPHPUnitIntegration;

    protected Container $app;

    /** @var \Mockery\MockInterface */
    protected $mockAuth;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the Facade application
        $this->app = new Container;
        Container::setInstance($this->app);
        Facade::setFacadeApplication($this->app);
        Facade::clearResolvedInstance('auth');
        Facade::clearResolvedInstance('db');
        Facade::clearResolvedInstances();

        // Register the auth mock
        $this->mockAuth = Mockery::mock();
        $this->app->instance('auth', $this->mockAuth);

        // Minimal bindings for external Facade dependencies (no assertions in tests)
        $this->app->instance('log', new class
        {
            public function info(): void {}

            public function warning(): void {}

            public function error(): void {}

            public function debug(): void {}
        });
        $this->app->instance('config', new class
        {
            public function get($key, $default = null)
            {
                return 'dummy';
            }
        });
        $this->app->instance('request', Request::create('/', 'GET'));

        // Register the translator mock
        $this->app->instance('translator', new class
        {
            public function get($key, $replace = [], $locale = null)
            {
                // Return specific translations for known keys
                $translations = [
                    'errors.business.record_not_found' => 'Student word learning record not found.',
                    'errors.business.duplicate_answer' => 'This question has already been answered in this lesson.',
                ];

                return $translations[$key] ?? $key;
            }
        });

        // Register the DB mock
        $mockDb = Mockery::mock();
        $mockDb->shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });
        $this->app->instance('db', $mockDb);

    }

    /**
     * Set the authenticated user ID.
     */
    protected function setAuthUserId(int $userId, array $additionalProperties = []): void
    {
        $user = (object) array_merge(['id' => $userId], $additionalProperties);
        $this->mockAuth->shouldReceive('user')->andReturn($user);
        $this->mockAuth->shouldReceive('id')->andReturn($userId);
    }

    protected function tearDown(): void
    {
        // Unset the Facade application reference
        Facade::setFacadeApplication(null);
        // Reset Carbon test time
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    /**
     * テスト用の現在時刻を設定
     *
     * @param  string  $testDateTime  テスト用の日時 (Y-m-d H:i:s形式)
     */
    protected function mockTestDate(string $testDateTime): void
    {
        Carbon::setTestNow($testDateTime);
    }

    /**
     * テスト用時刻設定をリセット
     */
    protected function resetTestDate(): void
    {
        Carbon::setTestNow(null);
    }
}
