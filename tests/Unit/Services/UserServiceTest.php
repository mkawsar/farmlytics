<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class UserServiceTest extends ServiceTestCase
{
    #[Test]
    public function test_user_service_can_get_all_users(): void
    {
        /*
         * Given: A user service with a repository that returns a collection of users
         * When: The user service is called to get all users
         * Then: The user service returns an Eloquent collection of users
         */
        $user = new User;
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $repo = Mockery::mock(UserRepository::class);
        $repo->shouldReceive('getAllUsers')
            ->once()
            ->andReturn(new EloquentCollection([$user]));

        $service = new UserService($repo);
        $users = $service->getAllUsers();

        $this->assertInstanceOf(EloquentCollection::class, $users);
        $this->assertCount(1, $users);
        $this->assertSame($user, $users->first());
    }
}
