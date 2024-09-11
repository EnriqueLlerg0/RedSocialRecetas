<?php

namespace App\Http\Controllers;

use App\Models\Recipes;
use App\Models\User;
use Illuminate\Http\Request;

class userDataController extends Controller
{
    public function updateImage(Request $request)
    {
        $usu=User::where('id',$request->idUser)->first();

        $usu->img=$request->img;
        $usu->save();
    }
    public function updateUsername(Request $request)
    {
        $attributes=request()->validate([
            'Username'=>'required|max:255|unique:users,user',
        ]);

        $usu=User::where('id',$request->idUser)->first();

        $usu->user=$request->Username;
        $usu->save();
    }
    public function updateEmail(Request $request)
    {
        $attributes=request()->validate([
            'email'=>'required|email|max:255|unique:users,email',
        ]);

        $usu=User::where('id',$request->idUser)->first();

        $usu->email=$request->email;
        $usu->save();
    }
    public function updateContraseña(Request $request)
    {
        $usu=User::where('id',$request->idUser)->first();

        $usu->password = bcrypt($request->input('contra'));
        $usu->save();
    }
    public function updateContraseñaNoData(Request $request)
    {
        $usu=User::where('email',$request->email)->first();

        $usu->password = bcrypt($request->input('contra'));
        $usu->save();
    }
    public function getAllRecipeUser(Request $request)
    {
        $recipes=Recipes::where('id_user', $request->idUser)->get();

        return response()->json(['recipe' => $recipes], 200);
    }
    public function getAllDataUser(Request $request)
    {
        $usu=User::where('id',$request->idUser)->first();

        return response()->json(['dataUser' => $usu], 200);
    }
}
