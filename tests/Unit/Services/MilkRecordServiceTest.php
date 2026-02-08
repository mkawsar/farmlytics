<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Animal;
use App\Models\MilkRecord;
use App\Repositories\MilkRecordRepository;
use App\Services\AnimalService;
use App\Services\MilkRecordService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class MilkRecordServiceTest extends ServiceTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function create_for_animal_delegates_to_repo_with_animal_shed_and_farm_ids(): void
    {
        /*
         * Given: an animal with shed_id and farm_id, and milk record data
         * When: we create a milk record for that animal
         * Then: the repository is called with animal_id, shed_id and farm_id from the animal, and the created record is returned
         */
        $animalId = 1;
        $animal = new Animal;
        $animal->id = $animalId;
        $animal->shed_id = 10;
        $animal->farm_id = 20;

        $data = [
            'record_date' => '2026-02-07',
            'quantity_liter' => 12.5,
            'notes' => 'Morning milking',
        ];
        $record = new MilkRecord;
        $record->id = 1;

        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('createRecord')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($animalId) {
                return $arg['animal_id'] === $animalId
                    && $arg['shed_id'] === 10
                    && $arg['farm_id'] === 20
                    && isset($arg['record_date'], $arg['quantity_liter'], $arg['notes']);
            }))
            ->andReturn($record);

        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getById')->once()->with($animalId)->andReturn($animal);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->createForAnimal($animalId, $data);

        $this->assertInstanceOf(MilkRecord::class, $result);
        $this->assertSame($record, $result);
    }

    #[Test]
    public function create_for_animal_with_user_id_adds_created_by_to_data(): void
    {
        /*
         * Given: an animal and milk record data, and a user id
         * When: we create a milk record for that animal with user id
         * Then: the repository is called with created_by set to the user id
         */
        $animalId = 1;
        $animal = new Animal;
        $animal->id = $animalId;
        $animal->shed_id = 10;
        $animal->farm_id = 20;

        $data = [
            'record_date' => '2026-02-07',
            'quantity_liter' => 15.0,
        ];
        $record = new MilkRecord;
        $record->id = 1;

        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('createRecord')
            ->once()
            ->with(Mockery::on(function (array $arg) {
                return $arg['created_by'] === 99;
            }))
            ->andReturn($record);

        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getById')->once()->with($animalId)->andReturn($animal);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->createForAnimal($animalId, $data, 99);

        $this->assertSame($record, $result);
    }

    #[Test]
    public function get_by_id_delegates_to_repository(): void
    {
        /*
         * Given: a milk record id and a repository that returns that record
         * When: we get the record by id
         * Then: the repository is called and the record is returned
         */
        $id = 1;
        $record = new MilkRecord;
        $record->id = $id;

        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('getRecordById')->once()->with($id)->andReturn($record);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->getById($id);

        $this->assertSame($record, $result);
    }

    #[Test]
    public function update_delegates_to_repository(): void
    {
        /*
         * Given: a milk record id and update data
         * When: we update the record
         * Then: the repository is called and the updated record is returned
         */
        $id = 1;
        $data = ['quantity_liter' => 14.0, 'notes' => 'Updated'];
        $record = new MilkRecord;
        $record->id = $id;

        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('updateRecord')->once()->with($id, $data)->andReturn($record);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->update($id, $data);

        $this->assertSame($record, $result);
    }

    #[Test]
    public function delete_delegates_to_repository(): void
    {
        /*
         * Given: a milk record id and a repository that returns true on delete
         * When: we delete the record
         * Then: the repository delete is called and true is returned
         */
        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('deleteRecord')->once()->with(1)->andReturn(true);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function get_paginated_by_animal_delegates_to_repository(): void
    {
        /*
         * Given: an animal id and a repository that returns a paginator
         * When: we get milk records paginated by animal
         * Then: the repository is called and the paginator is returned
         */
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $milkRepo = Mockery::mock(MilkRecordRepository::class);
        $milkRepo->shouldReceive('getPaginatedByAnimal')->once()->with(1, 15)->andReturn($paginator);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new MilkRecordService($milkRepo, $animalService);
        $result = $service->getPaginatedByAnimal(1, 15);

        $this->assertSame($paginator, $result);
    }
}
