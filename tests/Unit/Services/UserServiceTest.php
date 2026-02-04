<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class UserServiceTest extends ServiceTestCase
{
    #[Test]
    public function test_user_service_can_get_all_users(): void
    {
        /*
        * Intent (Given / When / Then)
        * Given: A user service
        * When: The user service is called to get all users
        * Then: The user service returns a collection of users
        */
        $repo = Mockery::mock(UserRepository::class);
        $repo->shouldReceive('getAllUsers')
            ->once()
            ->andReturn(collect([(object) ['id' => 1]]));

        $service = new UserService($repo);
        $users = $service->getAllUsers();
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(1, $users);
    }
}
