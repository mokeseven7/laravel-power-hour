<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeTokenController extends Controller
{
    public function issue(Request $request){
        $token = $request->user()->createToken('apiuser', ['employee:read', 'employee:write']);
 
        return response()->json(['token' => $token->plainTextToken, $token]);
    }
}
