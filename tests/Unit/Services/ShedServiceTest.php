<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Shed;
use App\Repositories\ShedRepository;
use App\Services\ShedService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class ShedServiceTest extends ServiceTestCase
{
    #[Test]
    public function get_paginated_by_farm_delegates_to_repository_and_returns_paginator(): void
    {
        /*
         * Given: A shed service with a repository that returns a paginator
         * When: The service is called to get sheds for a farm with search
         * Then: The repository is called with farmId, search and perPage, and the paginator is returned
         */
        $farmId = 1;
        $search = 'north';
        $perPage = 15;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('getShedsPaginatedByFarm')
            ->once()
            ->with($farmId, $search, $perPage)
            ->andReturn($paginator);

        $service = new ShedService($repo);
        $result = $service->getPaginatedByFarm($farmId, $search, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_paginated_by_farm_passes_null_when_search_empty(): void
    {
        /*
         * Given: A shed service with a repository
         * When: The service is called with empty string search
         * Then: The repository is called with null for search (search ?: null)
         */
        $farmId = 1;
        $perPage = 10;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('getShedsPaginatedByFarm')
            ->once()
            ->with($farmId, null, $perPage)
            ->andReturn($paginator);

        $service = new ShedService($repo);
        $service->getPaginatedByFarm($farmId, '', $perPage);
    }

    #[Test]
    public function get_by_id_delegates_to_repository_and_returns_shed(): void
    {
        /*
         * Given: A shed service with a repository that returns a shed
         * When: The service is called to get a shed by id
         * Then: The repository is called with the id and the shed is returned
         */
        $shed = new Shed;
        $shed->id = 1;
        $shed->name = 'North Shed';

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('getShedById')
            ->once()
            ->with(1)
            ->andReturn($shed);

        $service = new ShedService($repo);
        $result = $service->getById(1);

        $this->assertInstanceOf(Shed::class, $result);
        $this->assertSame($shed, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('North Shed', $result->name);
    }

    #[Test]
    public function create_delegates_to_repository_with_farm_id_without_user_id(): void
    {
        /*
         * Given: A shed service with a repository and create data without a user id
         * When: The service is called to create a shed with no user id
         * Then: The repository is called with data including farm_id and the created shed is returned
         */
        $farmId = 1;
        $data = ['name' => 'New Shed', 'type' => 'milking', 'capacity' => 50];
        $expectedData = $data + ['farm_id' => $farmId];
        $shed = new Shed;
        $shed->id = 1;
        $shed->name = $data['name'];

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('createShed')
            ->once()
            ->with($expectedData)
            ->andReturn($shed);

        $service = new ShedService($repo);
        $result = $service->create($farmId, $data);

        $this->assertSame($shed, $result);
    }

    #[Test]
    public function create_adds_created_by_and_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: A shed service with a repository and a user id
         * When: The service is called to create a shed with the user id
         * Then: The repository is called with data including farm_id, created_by and updated_by
         */
        $farmId = 1;
        $data = ['name' => 'New Shed', 'type' => 'calf'];
        $userId = 42;

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('createShed')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($farmId, $userId) {
                return $arg['farm_id'] === $farmId
                    && $arg['created_by'] === $userId
                    && $arg['updated_by'] === $userId
                    && $arg['name'] === 'New Shed';
            }))
            ->andReturn(new Shed);

        $service = new ShedService($repo);
        $service->create($farmId, $data, $userId);
    }

    #[Test]
    public function update_delegates_to_repository_without_user_id(): void
    {
        /*
         * Given: A shed service with a repository that returns an updated shed
         * When: The service is called to update a shed with no user id
         * Then: The repository is called with id and data as-is and the shed is returned
         */
        $id = 1;
        $data = ['name' => 'Updated Shed'];
        $shed = new Shed;
        $shed->id = $id;
        $shed->name = $data['name'];

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('updateShed')
            ->once()
            ->with($id, $data)
            ->andReturn($shed);

        $service = new ShedService($repo);
        $result = $service->update($id, $data);

        $this->assertSame($shed, $result);
    }

    #[Test]
    public function update_adds_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: A shed service with a repository and a user id
         * When: The service is called to update a shed with the user id
         * Then: The repository is called with data including updated_by set to the user id
         */
        $id = 1;
        $data = ['name' => 'Updated Shed'];
        $userId = 42;

        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('updateShed')
            ->once()
            ->with($id, Mockery::on(function (array $arg) {
                return $arg['updated_by'] === 42 && $arg['name'] === 'Updated Shed';
            }))
            ->andReturn(new Shed);

        $service = new ShedService($repo);
        $service->update($id, $data, $userId);
    }

    #[Test]
    public function update_returns_null_when_repository_returns_null(): void
    {
        /*
         * Given: A shed service with a repository that returns null (shed not found)
         * When: The service is called to update a non-existent shed
         * Then: The service returns null
         */
        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('updateShed')
            ->once()
            ->with(999, ['name' => 'X'])
            ->andReturn(null);

        $service = new ShedService($repo);
        $result = $service->update(999, ['name' => 'X']);

        $this->assertNull($result);
    }

    #[Test]
    public function delete_delegates_to_repository_and_returns_boolean(): void
    {
        /*
         * Given: A shed service with a repository that returns true
         * When: The service is called to delete a shed by id
         * Then: The repository is called with the id and true is returned
         */
        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('deleteShed')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new ShedService($repo);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function delete_returns_false_when_repository_returns_false(): void
    {
        /*
         * Given: A shed service with a repository that returns false (shed not found)
         * When: The service is called to delete a non-existent shed
         * Then: The service returns false
         */
        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('deleteShed')
            ->once()
            ->with(999)
            ->andReturn(false);

        $service = new ShedService($repo);
        $result = $service->delete(999);

        $this->assertFalse($result);
    }

    #[Test]
    public function delete_many_delegates_to_repository_and_returns_count(): void
    {
        /*
         * Given: A shed service with a repository that returns a count of 2
         * When: The service is called to delete multiple sheds by ids
         * Then: The repository is called with the ids and the count is returned
         */
        $ids = [1, 2];
        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('deleteShedsByIds')
            ->once()
            ->with($ids)
            ->andReturn(2);

        $service = new ShedService($repo);
        $result = $service->deleteMany($ids);

        $this->assertSame(2, $result);
    }

    #[Test]
    public function delete_many_returns_zero_when_repository_deletes_none(): void
    {
        /*
         * Given: A shed service with a repository that returns 0 (no sheds deleted)
         * When: The service is called to delete multiple sheds
         * Then: The service returns 0
         */
        $ids = [99, 100];
        $repo = Mockery::mock(ShedRepository::class);
        $repo->shouldReceive('deleteShedsByIds')
            ->once()
            ->with($ids)
            ->andReturn(0);

        $service = new ShedService($repo);
        $result = $service->deleteMany($ids);

        $this->assertSame(0, $result);
    }
}
