<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EmployeeResourceTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @return void
     */
    public function test_employee_http_endpoints_gutcheck()
    {
        $response = $this->get('/api/employees');
        $response->assertStatus(200);
    }

    /**
     * Test that resources are returned when we have them in the database
     *
     * @return void
     */
    public function test_index_returns_collection_of_employees()
    {
        $employees = Employee::factory()->count(1)->create();
        
        $response = $this->get('/api/employees');

        $response->assertJson($employees->toArray());
    }

    public function test_fetch_by_id_returns_one_employee(){
        $employees = Employee::factory()->count(1)->create(['name' => 'bob smith']);

        $response = $this->get("/api/employees/{$employees->take(1)->get('id')}");

        $response->assertJson($employees->toArray());
    }


    public function test_post_employee_validation(){
        $response = $this->postJson('/api/employees',['']);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['email' => [], 'name' => [], 'employee_id' => []]]);
    }

    public function test_post_employee_stores_employee(){
        $json_body = ['name' => 'mike', 'email'=>'test@test.com', 'employee_id'=> '12-ABCD'];
        $response = $this->postJson('/api/employees', $json_body);
    
        $response->assertStatus(200);
        $response->assertJson($json_body);
    }

    public function test_put_employee_validation(){
        $id = 101;
        Employee::factory()->count(1)->create(['id' => $id, 'name' => 'ok', 'email' => 'test@test.com', 'employee_id' => '00-AAAA']);
        
        $response = $this->putJson("/api/employees/{$id}", ['']);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['id' => []]]);
    }


    public function test_put_action_updates_employee(){
       
        $employee = Employee::factory()->create();
        $response = $this->putJson("/api/employees/" . $employee->id, ['id' => $employee->id, 'name' => Str::reverse($employee->name)]);
       
       
        $this->assertNotEquals($employee->name, $response->json('name'));
        
    }

    public function test_delete_action_deletes_employee(){
        $id = 999;
        $employee_data = ['id' => $id, 'name' => 'bob smith', 'email' => 'old@old.com', 'employee_id' => '00-AAAA'];

        $employee = Employee::factory()->count(1)->create($employee_data);

        $this->assertDatabaseHas('employees', ['id' => $id]);

        $response = $this->deleteJson("/api/employees/{$id}");

        $this->assertDatabaseMissing('employees', ['id' => $id]);
    }
   
}

