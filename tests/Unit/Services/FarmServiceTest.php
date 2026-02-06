<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Farm;
use App\Repositories\FarmRepository;
use App\Services\FarmService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class FarmServiceTest extends ServiceTestCase
{
    #[Test]
    public function get_all_paginated_delegates_to_repository_and_returns_paginator(): void
    {
        /*
         * Given: A farm service with a repository that returns a paginator
         * When: The service is called to get all farms paginated
         * Then: The repository is called with conditions and perPage, and the paginator is returned
         */
        $conditions = [];
        $perPage = 15;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('getAllFarmsPaginated')
            ->once()
            ->with($conditions, $perPage)
            ->andReturn($paginator);

        $service = new FarmService($repo);
        $result = $service->getAllPaginated($conditions, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_all_paginated_with_search_delegates_to_repository_and_returns_paginator(): void
    {
        /*
         * Given: A farm service with a repository that returns a paginator
         * When: The service is called with a search term
         * Then: The repository is called with search and perPage, and the paginator is returned
         */
        $search = 'north';
        $perPage = 10;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('getFarmsPaginatedWithSearch')
            ->once()
            ->with($search, $perPage)
            ->andReturn($paginator);

        $service = new FarmService($repo);
        $result = $service->getAllPaginatedWithSearch($search, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_all_paginated_with_search_passes_null_when_search_empty(): void
    {
        /*
         * Given: A farm service with a repository
         * When: The service is called with null search
         * Then: The repository is called with null for search and the given perPage
         */
        $perPage = 10;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('getFarmsPaginatedWithSearch')
            ->once()
            ->with(null, $perPage)
            ->andReturn($paginator);

        $service = new FarmService($repo);
        $service->getAllPaginatedWithSearch(null, $perPage);
    }

    #[Test]
    public function get_by_id_delegates_to_repository_and_returns_farm(): void
    {
        /*
         * Given: A farm service with a repository that returns a farm
         * When: The service is called to get a farm by id
         * Then: The repository is called with the id and the farm is returned
         */
        $farm = new Farm;
        $farm->id = 1;
        $farm->name = 'Test Farm';

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('getFarmById')
            ->once()
            ->with(1)
            ->andReturn($farm);

        $service = new FarmService($repo);
        $result = $service->getById(1);

        $this->assertInstanceOf(Farm::class, $result);
        $this->assertSame($farm, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('Test Farm', $result->name);
    }

    #[Test]
    public function create_delegates_to_repository_without_user_id(): void
    {
        /*
         * Given: A farm service with a repository and create data without a user id
         * When: The service is called to create a farm with no user id
         * Then: The repository is called with the data as-is and the created farm is returned
         */
        $data = ['name' => 'New Farm', 'location' => 'Here', 'type' => 'dairy', 'capacity' => 100];
        $farm = new Farm;
        $farm->id = 1;
        $farm->name = $data['name'];

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('createFarm')
            ->once()
            ->with($data)
            ->andReturn($farm);

        $service = new FarmService($repo);
        $result = $service->create($data);

        $this->assertSame($farm, $result);
    }

    #[Test]
    public function create_adds_created_by_and_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: A farm service with a repository and a user id
         * When: The service is called to create a farm with the user id
         * Then: The repository is called with data including created_by and updated_by set to the user id
         */
        $data = ['name' => 'New Farm', 'location' => 'Here', 'type' => 'mixed'];
        $userId = 42;
        $expectedData = $data + ['created_by' => $userId, 'updated_by' => $userId];
        $farm = new Farm;
        $farm->id = 1;

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('createFarm')
            ->once()
            ->with(Mockery::on(function (array $arg) {
                return $arg['created_by'] === 42
                    && $arg['updated_by'] === 42
                    && $arg['name'] === 'New Farm';
            }))
            ->andReturn($farm);

        $service = new FarmService($repo);
        $service->create($data, $userId);
    }

    #[Test]
    public function update_delegates_to_repository_without_user_id(): void
    {
        /*
         * Given: A farm service with a repository that returns an updated farm
         * When: The service is called to update a farm with no user id
         * Then: The repository is called with id and data as-is and the farm is returned
         */
        $id = 1;
        $data = ['name' => 'Updated Farm'];
        $farm = new Farm;
        $farm->id = $id;
        $farm->name = $data['name'];

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('updateFarm')
            ->once()
            ->with($id, $data)
            ->andReturn($farm);

        $service = new FarmService($repo);
        $result = $service->update($id, $data);

        $this->assertSame($farm, $result);
    }

    #[Test]
    public function update_adds_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: A farm service with a repository and a user id
         * When: The service is called to update a farm with the user id
         * Then: The repository is called with data including updated_by set to the user id
         */
        $id = 1;
        $data = ['name' => 'Updated Farm'];
        $userId = 42;

        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('updateFarm')
            ->once()
            ->with($id, Mockery::on(function (array $arg) {
                return $arg['updated_by'] === 42 && $arg['name'] === 'Updated Farm';
            }))
            ->andReturn(new Farm);

        $service = new FarmService($repo);
        $service->update($id, $data, $userId);
    }

    #[Test]
    public function update_returns_null_when_repository_returns_null(): void
    {
        /*
         * Given: A farm service with a repository that returns null (farm not found)
         * When: The service is called to update a non-existent farm
         * Then: The service returns null
         */
        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('updateFarm')
            ->once()
            ->with(999, ['name' => 'X'])
            ->andReturn(null);

        $service = new FarmService($repo);
        $result = $service->update(999, ['name' => 'X']);

        $this->assertNull($result);
    }

    #[Test]
    public function delete_delegates_to_repository_and_returns_boolean(): void
    {
        /*
         * Given: A farm service with a repository that returns true
         * When: The service is called to delete a farm by id
         * Then: The repository is called with the id and true is returned
         */
        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('deleteFarm')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new FarmService($repo);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function delete_returns_false_when_repository_returns_false(): void
    {
        /*
         * Given: A farm service with a repository that returns false (farm not found)
         * When: The service is called to delete a non-existent farm
         * Then: The service returns false
         */
        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('deleteFarm')
            ->once()
            ->with(999)
            ->andReturn(false);

        $service = new FarmService($repo);
        $result = $service->delete(999);

        $this->assertFalse($result);
    }

    #[Test]
    public function delete_many_delegates_to_repository_and_returns_count(): void
    {
        /*
         * Given: A farm service with a repository that returns a count of 3
         * When: The service is called to delete multiple farms by ids
         * Then: The repository is called with the ids and the count is returned
         */
        $ids = [1, 2, 3];
        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('deleteFarmsByIds')
            ->once()
            ->with($ids)
            ->andReturn(3);

        $service = new FarmService($repo);
        $result = $service->deleteMany($ids);

        $this->assertSame(3, $result);
    }

    #[Test]
    public function delete_many_returns_zero_when_repository_deletes_none(): void
    {
        /*
         * Given: A farm service with a repository that returns 0 (no farms deleted)
         * When: The service is called to delete multiple farms
         * Then: The service returns 0
         */
        $ids = [99, 100];
        $repo = Mockery::mock(FarmRepository::class);
        $repo->shouldReceive('deleteFarmsByIds')
            ->once()
            ->with($ids)
            ->andReturn(0);

        $service = new FarmService($repo);
        $result = $service->deleteMany($ids);

        $this->assertSame(0, $result);
    }
}
