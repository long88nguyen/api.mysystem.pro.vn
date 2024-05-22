<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(){
        $data = Employee::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $result = Employee::create([
            'name' => $request['name'],
            'age' => $request['age'],
        ]);

        return response()->json([
            'data' => $result,
            'status' => true,
        ]);
    }

    public function delete($id)
    {
        Employee::find($id)->delete();
        return response()->json([
            'data' => [],
            'status' => true,
        ]);
    }
}
