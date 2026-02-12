<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Animal;
use App\Models\Shed;
use App\Repositories\AnimalRepository;
use App\Services\AnimalService;
use App\Services\ShedService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class AnimalServiceTest extends ServiceTestCase
{
    #[Test]
    public function get_paginated_delegates_to_repository_and_returns_paginator(): void
    {
        /*
         * Given: An animal service with a repository that returns a paginator
         * When: The service is called to get all animals with search
         * Then: The repository is called with search and perPage, and the paginator is returned
         */
        $search = 'RFID';
        $perPage = 15;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getAnimalsPaginated')
            ->once()
            ->with($search, $perPage)
            ->andReturn($paginator);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->getPaginated($search, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_paginated_passes_null_when_search_empty(): void
    {
        /*
         * Given: An animal service with a repository
         * When: The service is called with empty string search
         * Then: The repository is called with null for search
         */
        $perPage = 10;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getAnimalsPaginated')
            ->once()
            ->with(null, $perPage)
            ->andReturn($paginator);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $service->getPaginated('', $perPage);
    }

    #[Test]
    public function get_paginated_by_shed_delegates_to_repository_and_returns_paginator(): void
    {
        /*
         * Given: An animal service with a repository that returns a paginator
         * When: The service is called to get animals for a shed with search
         * Then: The repository is called with shedId, search and perPage, and the paginator is returned
         */
        $shedId = 1;
        $search = 'RFID';
        $perPage = 15;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getAnimalsPaginatedByShed')
            ->once()
            ->with($shedId, $search, $perPage)
            ->andReturn($paginator);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->getPaginatedByShed($shedId, $search, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_paginated_by_shed_passes_null_when_search_empty(): void
    {
        /*
         * Given: An animal service with a repository
         * When: The service is called with empty string search for a shed
         * Then: The repository is called with null for search
         */
        $shedId = 1;
        $perPage = 10;
        $paginator = new LengthAwarePaginatorImpl([], 0, $perPage, 1);

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getAnimalsPaginatedByShed')
            ->once()
            ->with($shedId, null, $perPage)
            ->andReturn($paginator);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $service->getPaginatedByShed($shedId, '', $perPage);
    }

    #[Test]
    public function get_by_id_delegates_to_repository_and_returns_animal(): void
    {
        /*
         * Given: An animal service with a repository that returns an animal
         * When: The service is called to get an animal by id
         * Then: The repository is called with the id and the animal is returned
         */
        $animal = new Animal;
        $animal->id = 1;
        $animal->animal_id = 'RFID-001';

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getAnimalById')
            ->once()
            ->with(1)
            ->andReturn($animal);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->getById(1);

        $this->assertInstanceOf(Animal::class, $result);
        $this->assertSame($animal, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('RFID-001', $result->animal_id);
    }

    #[Test]
    public function create_delegates_to_repository_with_shed_id_and_farm_id_from_shed(): void
    {
        /*
         * Given: An animal service with a repository and a shed (with farm_id)
         * When: The service is called to create an animal for that shed
         * Then: The repository is called with data including shed_id, farm_id, and generated animal_id (e.g. HF-YYYYMM-1)
         */
        $shedId = 1;
        $farmId = 5;
        $shed = new Shed;
        $shed->id = $shedId;
        $shed->farm_id = $farmId;

        $data = ['breed' => 'Holstein', 'gender' => 'female', 'status' => 'active'];

        $animal = new Animal;
        $animal->id = 1;
        $animal->animal_id = 'HF-202602-1';

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getNextSequenceForBreedCodeAndMonth')
            ->once()
            ->with('HF', Mockery::type('string'))
            ->andReturn(1);
        $repo->shouldReceive('createAnimal')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($shedId, $farmId) {
                return $arg['shed_id'] === $shedId
                    && $arg['farm_id'] === $farmId
                    && preg_match('/^HF-\d{6}-1$/', (string) ($arg['animal_id'] ?? '')) === 1;
            }))
            ->andReturn($animal);

        $shedService = Mockery::mock(ShedService::class);
        $shedService->shouldReceive('getById')
            ->once()
            ->with($shedId)
            ->andReturn($shed);

        $service = new AnimalService($repo, $shedService);
        $result = $service->create($shedId, $data);

        $this->assertSame($animal, $result);
    }

    #[Test]
    public function create_adds_created_by_and_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: An animal service with a repository and a user id
         * When: The service is called to create an animal with the user id
         * Then: The repository is called with data including created_by, updated_by, and generated animal_id
         */
        $shedId = 1;
        $shed = new Shed;
        $shed->id = $shedId;
        $shed->farm_id = 10;

        $data = ['breed' => 'Jersey', 'gender' => 'male', 'status' => 'active'];
        $userId = 42;

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('getNextSequenceForBreedCodeAndMonth')
            ->once()
            ->with('JY', Mockery::type('string'))
            ->andReturn(1);
        $repo->shouldReceive('createAnimal')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($userId) {
                return $arg['created_by'] === $userId
                    && $arg['updated_by'] === $userId
                    && preg_match('/^JY-\d{6}-1$/', (string) ($arg['animal_id'] ?? '')) === 1;
            }))
            ->andReturn(new Animal);

        $shedService = Mockery::mock(ShedService::class);
        $shedService->shouldReceive('getById')->once()->with($shedId)->andReturn($shed);

        $service = new AnimalService($repo, $shedService);
        $service->create($shedId, $data, $userId);
    }

    #[Test]
    public function update_delegates_to_repository_without_user_id(): void
    {
        /*
         * Given: An animal service with a repository that returns an updated animal
         * When: The service is called to update an animal with no user id
         * Then: The repository is called with id and data as-is and the animal is returned
         */
        $id = 1;
        $data = ['breed' => 'Updated Breed'];
        $animal = new Animal;
        $animal->id = $id;

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('updateAnimal')
            ->once()
            ->with($id, $data)
            ->andReturn($animal);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->update($id, $data);

        $this->assertSame($animal, $result);
    }

    #[Test]
    public function update_adds_updated_by_when_user_id_provided(): void
    {
        /*
         * Given: An animal service with a repository and a user id
         * When: The service is called to update an animal with the user id
         * Then: The repository is called with data including updated_by set to the user id
         */
        $id = 1;
        $data = ['breed' => 'Updated Breed'];
        $userId = 42;

        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('updateAnimal')
            ->once()
            ->with($id, Mockery::on(function (array $arg) use ($userId) {
                return $arg['updated_by'] === $userId;
            }))
            ->andReturn(new Animal);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $service->update($id, $data, $userId);
    }

    #[Test]
    public function update_returns_null_when_repository_returns_null(): void
    {
        /*
         * Given: An animal service with a repository that returns null (animal not found)
         * When: The service is called to update a non-existent animal
         * Then: The service returns null
         */
        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('updateAnimal')
            ->once()
            ->with(999, ['breed' => 'X'])
            ->andReturn(null);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->update(999, ['breed' => 'X']);

        $this->assertNull($result);
    }

    #[Test]
    public function delete_delegates_to_repository_and_returns_boolean(): void
    {
        /*
         * Given: An animal service with a repository that returns true
         * When: The service is called to delete an animal by id
         * Then: The repository is called with the id and true is returned
         */
        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('deleteAnimal')
            ->once()
            ->with(1)
            ->andReturn(true);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function delete_returns_false_when_repository_returns_false(): void
    {
        /*
         * Given: An animal service with a repository that returns false (animal not found)
         * When: The service is called to delete a non-existent animal
         * Then: The service returns false
         */
        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('deleteAnimal')
            ->once()
            ->with(999)
            ->andReturn(false);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->delete(999);

        $this->assertFalse($result);
    }

    #[Test]
    public function delete_many_delegates_to_repository_and_returns_count(): void
    {
        /*
         * Given: An animal service with a repository that returns a count of 2
         * When: The service is called to delete multiple animals by ids
         * Then: The repository is called with the ids and the count is returned
         */
        $ids = [1, 2];
        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('deleteAnimalsByIds')
            ->once()
            ->with($ids)
            ->andReturn(2);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->deleteMany($ids);

        $this->assertSame(2, $result);
    }

    #[Test]
    public function delete_many_returns_zero_when_repository_deletes_none(): void
    {
        /*
         * Given: An animal service with a repository that returns 0 (no animals deleted)
         * When: The service is called to delete multiple animals
         * Then: The service returns 0
         */
        $ids = [99, 100];
        $repo = Mockery::mock(AnimalRepository::class);
        $repo->shouldReceive('deleteAnimalsByIds')
            ->once()
            ->with($ids)
            ->andReturn(0);

        $shedService = Mockery::mock(ShedService::class);
        $service = new AnimalService($repo, $shedService);
        $result = $service->deleteMany($ids);

        $this->assertSame(0, $result);
    }
}
