<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Recipes;
use Illuminate\Http\Request;

class favoriteController extends Controller
{
    public function getRecipes(Request $request)
    {
        $id=[];
        $likeRecipes=Like::where('id_user',$request->idUser)->get();

        $query = Recipes::query();

        if($request->text!=null)
        {
            $text=$request->text;
            $query->where('name_recipe','like','%'.$text.'%');
        }

        foreach ($likeRecipes as $data)
        {
            $id[]=$data->id_receta;
        }

        $query->whereIn('id',$id)->get();

        $recipes=$query->paginate(6);

        foreach ($recipes as $data)
        {
            $data['favoriteState']=true;
        }

        return response()->json(['recipe' => $recipes], 200);
    }
}
