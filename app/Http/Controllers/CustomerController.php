<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show($document)
    {
        return response()->json([
            'customer' => Customer::where('document', '=', intval($document))->first()
        ], 200);
    }

    public function store(Request $request)
    {
        $customer = new Customer();

        $customer->name = $request->name;
        $customer->document = $request->document;
        $customer->phone = $request->phone;

        $customer->save();

        return response()->json(["result" => "ok"], 201);
    }
}
