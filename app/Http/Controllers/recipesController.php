<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\Ingredient;
use App\Models\Like;
use App\Models\Recipes;
use App\Models\User;
use Illuminate\Http\Request;

class recipesController extends Controller
{
    public function getRecipes(Request $request)
    {
        $query = Recipes::query();

        if($request->text!=null)
        {
            $text=$request->text;
            $query->where('name_recipe','like','%'.$text.'%');
        }

        if($request->country!=null)
        {
            $pais=$request->country;
            $query->where('nacionality',$pais);
        }

        if($request->type!=null)
        {

            $idRecipe=[];
            $idAmou=[];

            $ing=$request->type;
            $ingre=Ingredient::where('id_category',$ing)->get();
            foreach ($ingre as $item)
            {
                $idAmou[]=$item->id_amount;
            }

            $amou=Amount::whereIn('id',$idAmou)->get();
            foreach ($amou as $item)
            {
                $idRecipe[]=$item->id_receta;
            }

//            dd($idRecipe);

            $query->whereIn('id',$idRecipe)->get();
        }


        $query->where('id_user','!=',$request->idUser);

        $recipes=$query->paginate(6);

        foreach ($recipes as $data)
        {
            $estado=Like::where('id_user',$request->idUser)->where('id_receta',$data->id)->get();
            $total=Like::where('id_receta',$data->id)->count();
            $usuario=User::where('id',$data->id_user)->first();

            if(count($estado)>0)
            {
                $data['favoriteState']=true;
            }
            else
            {
                $data['favoriteState']=false;
            }

            $data['nick']=$usuario->user;
            $data['total']=$total;
        }

//        dd($recipes);

        return response()->json(['recipe' => $recipes], 200);
    }

    public function setFavorite(Request $request)
    {
        $s=new Like();
        $s->id_user=$request->idUser;
        $s->id_receta=$request->idRecipe;
        $s->save();
    }

    public function deleteFavorite(Request $request)
    {
        Like::where('id_user',$request->idUser)->where('id_receta',$request->idRecipe)->delete();
    }
}
