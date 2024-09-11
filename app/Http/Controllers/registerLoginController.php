<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class registerLoginController extends Controller
{
    public function storeUsuario(Request $request)
    {
//        dd($request->input('usu'));

        $attributes=request()->validate([
            'usu'=>'required|max:255|unique:users,user',
            'ema'=>'required|email|max:255|unique:users,email',
            'contra'=>'required|min:3|max:255'
        ]);

        $s = new Usuario();
        $s->user = $request->input('usu');
        $s->email = $request->input('ema');
        $s->password = bcrypt($request->input('contra'));
        $s->img = "";
        $s->roll = "client";
        $s->save();
//
//        session()->flash('success','Your account has been created.');
    }

    public function checkUsuario(Request $request)
    {
        $credentials=$request->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        $data=[];
        $user=User::where('email','=',$request->email)->first();

        if($user && Hash::check($request->password, $user->password))
        {
            $data=[
                "id"=>$user->id,
                "user"=>$user->user,
                "email"=>$user->email,
                "img"=>$user->img,
                "roll"=>$user->roll
            ];

            return response()->json(['status'=>$data], 200);
        }
        else
        {
            return response()->json(['status'=>false], 200);
        }
    }
}
