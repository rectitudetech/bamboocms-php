<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

uses(RefreshDatabase::class);

require_once __DIR__.'/AdministratorRoleDataset.php';

beforeEach(function () {
    $this->routes = [
        'index' => 'AdministratorRole.Admin.AdministratorRole.index',
        'store' => 'AdministratorRole.Admin.AdministratorRole.store',
        'show' => 'AdministratorRole.Admin.AdministratorRole.show',
        'update' => 'AdministratorRole.Admin.AdministratorRole.update',
        'destroy' => 'AdministratorRole.Admin.AdministratorRole.destroy',
        'bulkDestroy' => 'AdministratorRole.Admin.AdministratorRole.bulkDestroy',
    ];
    $this->modelClass = AdministratorRole::class;
    $this->tableName = (new $this->modelClass)->getTable();
});

afterEach(function () {});

it('can display list', function ($params, $expected, $expectedCount = 10, $factory = null) {
    ($factory ?? fn () => null)();

    $this->getJson($this->getRoute('index', $params))
        ->assertJsonCount($expectedCount, 'data')
        ->assertJson($expected)
        ->assertStatus(200);
})->with('index');

it('cannot display list with invalid data', function ($params, $expected, $expectedCount = 0, $factory = null) {
    ($factory ?? fn () => null)();

    $this->getJson($this->getRoute('index', $params))
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('indexInvalidData');

it('can store', function ($data, $expected, $expectedCount = 1, $factory = null) {
    ($factory ?? fn () => null)();

    $this->postJson($this->getRoute('store'), $data)
        ->assertJson([
            'data' => $expected,
        ])
        ->assertStatus(201);
    $this->assertDatabaseHas($this->tableName, $expected);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('store');

it('cannot store with invalid data', function ($data, $expected, $expectedCount = 0, $factory = null) {
    ($factory ?? fn () => null)();

    $this->postJson($this->getRoute('store'), $data)
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('storeInvalidData');

it('can show', function ($params, $expected, $factory = null) {
    ($factory ?? fn () => null)();

    $this->getJson($this->getRoute('show', $params))
        ->assertJson(['data' => $expected])
        ->assertStatus(200);
})->with('show');

it('cannot show with invalid data', function ($params, $expected, $factory = null) {
    ($factory ?? fn () => null)();

    $this->getJson($this->getRoute('show', $params))
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
})->with('showInvalidData');

it('cannot show a resource does not exist', function () {
    $this->getJson($this->getRoute('show', ['id' => 999]))
        ->assertJson(['message' => 'Sorry, the requested resource does not exist.'])
        ->assertStatus(404);
});

it('can update', function ($id, $data, $expected, $factory = null) {
    ($factory ?? fn () => null)();

    $this->putJson($this->getRoute('update', ['id' => $id]), $data)
        ->assertJson(['data' => $expected])
        ->assertStatus(200);
    $this->assertDatabaseHas($this->tableName, $expected);
})->with('update');

it('cannot update with invalid data', function ($id, $data, $expected, $factory = null) {
    ($factory ?? fn () => null)();

    $this->putJson($this->getRoute('update', ['id' => $id]), $data)
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
})->with('updateInvalidData');

it('cannot update a resource does not exist', function () {
    $this->putJson($this->getRoute('update', ['id' => 999]), [])
        ->assertJson(['message' => 'Sorry, the requested resource does not exist.'])
        ->assertStatus(404);
});

it('can delete', function ($data, $factory = null) {
    ($factory ?? fn () => null)();

    $this->deleteJson($this->getRoute('destroy', $data))
        ->assertJson(['message' => 'Success! The record has been deleted.'])
        ->assertStatus(200);
    $this->assertDatabaseMissing($this->tableName, $data);
})->with('destroy');

it('cannot delete a resource does not exist', function () {
    $this->deleteJson($this->getRoute('destroy', ['id' => 999]))
        ->assertJson(['message' => 'Sorry, the requested resource does not exist.'])
        ->assertStatus(404);
});

it('can bulk delete', function ($data, $factory = null) {
    ($factory ?? fn () => null)();

    $this->deleteJson($this->getRoute('bulkDestroy'), $data)
        ->assertJson(['message' => 'Success! The records has been deleted.'])
        ->assertStatus(200);
    foreach ($data['ids'] as $id) {
        $this->assertDatabaseMissing($this->tableName, ['id' => $id]);
    }
})->with('bulkDestroy');
