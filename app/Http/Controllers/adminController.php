<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class adminController extends Controller
{
    public function getAllUser()
    {
        $users=User::all();

        return response()->json(['users' => $users], 200);
    }
}
