<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Employee::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     * @throws 
     */
    public function store(StoreEmployeeRequest $request)
    {
	// The safe() method is an explicit call to validate(), which returns an instance of ValidatedRequest
	$employee = Employee::create($request->safe()->only(['name', 'email', 'employee_id']));

	return response()->json($employee); 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return response()->json($employee);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
	// Get the validated inputs, array filter without a callback removes empties 
	// so we dont have to care weather they sent 1,2 or 3 updated props
	$inputs = array_filter($request->safe()->only(['name', 'email', 'employee_id']));

        $employee = Employee::find($id);

	$employee->update($inputs);

        // Return the updated model, we have to call refresh since we resolved the model with route-model binding
        return response()->json($employee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::find($id)->delete();

        return response()->json(['message' => "Employee With ID {$id} deleted"]);
    }
}
