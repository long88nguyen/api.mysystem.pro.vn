<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request){
        $data = Employee::orderBy('id', 'desc')->paginate(5);
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
            'description' => $request['description'],
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

    public function autoStore()
    {
        $faker = \Faker\Factory::create();
        $data = Employee::create([
            'name' => $faker->name,
            'age' => $faker->numberBetween(1, 100),
        ]);
        return response()->json([
            'data' => $data,
            'status' => true,
        ]);
    }
}
