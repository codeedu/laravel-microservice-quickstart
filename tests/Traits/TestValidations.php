<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{
  protected abstract function routeStore(): string;
  protected abstract function routeUpdate(): string;  
  
  protected function assertInvalidationInStore(
    array $data,
    string $rule,
    array $ruleParams = []
  ) {
    $response = $this->json('POST', $this->routeStore(), $data);
    $fields = array_keys($data);
    $this->AssertInvalidationFields($response, $fields, $rule, $ruleParams);
  }

  protected function assertInvalidationInUpdate(
    array $data,
    string $rule,
    array $ruleParams = []
  ) {
    $response = $this->json('PUT', $this->routeUpdate(), $data);
    $fields = array_keys($data);
    $this->AssertInvalidationFields($response, $fields, $rule, $ruleParams);
  }

  protected function AssertInvalidationFields(
    TestResponse $response,
    array $fields,
    string $rule,
    array $ruleParams = []
  ) {
    $response->assertStatus(422)
    ->assertJsonValidationErrors($fields);
    foreach($fields as $fieldName) {
      $fieldName = str_replace('_', ' ', $fieldName);
      $response->assertJsonFragment([
        \Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
      ]);
    }
  }
}