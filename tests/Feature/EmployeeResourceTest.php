<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use App\Models\User;


class EmployeeAuthenticationTest extends TestCase
{
    public function test_non_authenticated_user_cant_generate_tokens(){
        $user = User::factory()->create();
        $response = $this->postJson('api/tokens/employee');

        $response->assertUnauthorized();
    }


    public function test_valid_user_can_generate_token_when_authenticated()
    {
        $user = User::factory()->apiuser()->create();

        $response = $this->withBasicAuth('admin', 'admin')->postJson('api/tokens/employee');

        $response->assertJsonFragment(['abilities' => ["employee:read","employee:write"]]);
    }


    public function test_employee_token_can_create_employees()
    {
        $user = User::factory()->apiuser()->create();
        $token = $this->withBasicAuth('admin', 'admin')->postJson('api/tokens/employee');
        $employee = Employee::factory()->make();        
        $response = $this->withToken($token->json('token'))->postJson('api/employees', $employee->toArray());
        
        $this->assertDatabaseHas('employees', [
            'id' => $response->json('id'),
            'name' => $response->json('name'),
            'email' => $response->json('email'),
        ]);
    }
}