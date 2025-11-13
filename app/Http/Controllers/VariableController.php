<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VariableController extends Controller
{
    //
    public function index(){
        $list = [
            ''
        ];

        return response()->json([
                'list' => $list,
            ]);
    }


}
