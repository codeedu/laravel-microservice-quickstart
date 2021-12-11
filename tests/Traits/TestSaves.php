<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
  protected abstract function routeStore(): string;
  protected abstract function routeUpdate(): string;
  protected abstract function model(): string;
  
  protected function assertStore(array $sendData, array $testDatabase, array $testJson = null): TestResponse
  {
    $response = $this->json('POST', $this->routeStore(), $sendData);
    if ($response->status() !== 201) {
      throw new \Exception("Response status must be 201, found: {$response->status()}:\n{$response->content()}");
    }
    $this->assertData($response, $testDatabase, $testJson);
    return $response;
  }

  protected function assertUpdate(array $sendData, array $testDatabase, array $testJson = null): TestResponse
  {
    $response = $this->json('PUT', $this->routeUpdate(), $sendData);
    if ($response->status() !== 200) {
      throw new \Exception("Response status must be 200, found: {$response->status()}:\n{$response->content()}");
    }
    $this->assertData($response, $testDatabase, $testJson);
    return $response;
  }

  private function assertData(TestResponse $response, array $testDatabase, array $testJson = null)
  {
    $model = $this->model();
    $table = (new $model)->getTable();
    $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
    $testResponse = $testJson ?? $testDatabase;
    $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
  }
}