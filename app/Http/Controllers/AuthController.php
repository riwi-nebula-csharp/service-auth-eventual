<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function ping()
    {
        return response()->json([
            'message' => 'pong desde controlador',
            'status'  => 200
        ]);
    }
}