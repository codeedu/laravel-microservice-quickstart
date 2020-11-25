<?php
declare(strict_types=1);


namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{
    /**
     * @param array $data
     * @param string $rule
     * @param array $ruleParams
     */
    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ){
        $response = $this->json('POST',$this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response,$fields, $rule, $ruleParams);
    }

    /**
     * @param array $data
     * @param string $rule
     * @param array $ruleParams
     */
    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ){
        $response = $this->json('PUT',$this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response,$fields, $rule, $ruleParams);
    }

    /**
     * @param TestResponse $response
     * @param array $fields
     * @param string $rule
     * @param array $ruleParams
     */
    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach($fields as $field){
            $fieldName = str_replace('_',' ',$field);
            $response->assertJsonFragment([
                \Lang::get("validation.{$rule}",['attribute' => $fieldName] + $ruleParams)]);
        }

    }
}
