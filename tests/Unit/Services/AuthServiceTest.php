<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\AuthRepository;
use App\Services\AuthService;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class AuthServiceTest extends ServiceTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Facade::clearResolvedInstance('hash');
        $this->bindDefaultHasher();
    }

    protected function bindDefaultHasher(?bool $checkResult = null): void
    {
        $mockHasher = Mockery::mock(Hasher::class)->shouldIgnoreMissing();
        $mockHasher->shouldReceive('make')->andReturnUsing(fn (string $value) => $value);
        $mockHasher->shouldReceive('check')->andReturn($checkResult ?? false);
        $mockHasher->shouldReceive('isHashed')->andReturn(true);
        $mockHasher->shouldReceive('verifyConfiguration')->andReturn(true);
        $this->app->instance('hash', $mockHasher);
    }

    #[Test]
    public function attempt_login_returns_null_when_user_not_found(): void
    {
        /*
         * Given: AuthRepository returns null for the email
         * When: attemptLogin is called with that email
         * Then: returns null
         */
        $repo = Mockery::mock(AuthRepository::class);
        $repo->shouldReceive('findByEmail')
            ->once()
            ->with('unknown@example.com')
            ->andReturn(null);

        $service = new AuthService($repo);
        $result = $service->attemptLogin([
            'email' => 'unknown@example.com',
            'password' => 'any',
        ]);

        $this->assertNull($result);
    }

    #[Test]
    public function attempt_login_returns_null_when_password_does_not_match(): void
    {
        /*
         * Given: AuthRepository returns a user, Hash::check returns false
         * When: attemptLogin is called with wrong password
         * Then: returns null
         */
        $user = new User;
        $user->email = 'user@example.com';
        $user->password = 'hashed_password';

        $repo = Mockery::mock(AuthRepository::class);
        $repo->shouldReceive('findByEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn($user);

        $service = new AuthService($repo);
        $result = $service->attemptLogin([
            'email' => 'user@example.com',
            'password' => 'wrong_password',
        ]);

        $this->assertNull($result);
    }

    #[Test]
    public function attempt_login_returns_user_when_credentials_are_valid(): void
    {
        /*
         * Given: AuthRepository returns a user, Hash::check returns true
         * When: attemptLogin is called with correct email and password
         * Then: returns the user
         */
        $user = new User;
        $user->id = 1;
        $user->email = 'user@example.com';
        $user->password = 'hashed_password';

        $repo = Mockery::mock(AuthRepository::class);
        $repo->shouldReceive('findByEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn($user);

        Facade::clearResolvedInstance('hash');
        $mockHasher = Mockery::mock(Hasher::class)->shouldIgnoreMissing();
        $mockHasher->shouldReceive('make')->andReturnUsing(fn (string $value) => $value);
        $mockHasher->shouldReceive('check')
            ->once()
            ->with('correct_password', 'hashed_password')
            ->andReturn(true);
        $mockHasher->shouldReceive('isHashed')->andReturn(true);
        $mockHasher->shouldReceive('verifyConfiguration')->andReturn(true);
        $this->app->instance('hash', $mockHasher);

        $service = new AuthService($repo);
        $result = $service->attemptLogin([
            'email' => 'user@example.com',
            'password' => 'correct_password',
        ]);

        $this->assertSame($user, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('user@example.com', $result->email);
    }
}
