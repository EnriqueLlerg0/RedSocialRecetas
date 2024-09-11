<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Recipes;
use App\Models\User;
use Illuminate\Http\Request;

class recipeDataController extends Controller
{
    public function getRecipe(Request $request)
    {
        $cant=[];
        $idAmount=[];
        $ingredientes=[];
        $result=[];
        $tipoIngrediente=[];
        $ingredienteCant=[];

        $recipe=Recipes::where('name_recipe',$request->nameRecipe)->first();

        $amount=Amount::where('id_receta',$recipe->id)->get();

        foreach ($amount as $data)
        {
            $cant[]=$data->cantidad;
            $idAmount[]=$data->id;
        }

        $ingredient=Ingredient::whereIn('id_amount',$idAmount)->get();

        $id=$recipe->id;
        $img=$recipe->img;
        $name=$recipe->name_recipe;
        $descripcion=$recipe->description;
        $nacionalidad=$recipe->nacionality;

        for($i=0;$i<count($ingredient);$i++)
        {
            $ingredientes[]=$ingredient[$i]->name_ingredient;
            $tipoIngrediente []=$ingredient[$i]->id_category;
            $ingredienteCant[]=$ingredient[$i]->name_ingredient." ".$cant[$i];
        }

        $result=[
            "id"=>$id,
            "img"=>$img,
            "name"=>$name,
            "descripcion"=>$descripcion,
            "cantidad"=>$cant,
            "ingrediente"=>$ingredientes,
            "cate"=>$tipoIngrediente,
            "ingredienteCant"=>$ingredienteCant,
            "nacionalidad"=>$nacionalidad
        ];

        return response()->json(['recipe' => $result], 200);
    }

    public function getComent(Request $request)
    {
        $comment=Comment::where("id_receta",$request->id_receta)->get();

        foreach ($comment as $data)
        {
            $user=User::where('id',$data->id_user)->first();
//            dd($user);
            $data['img']=$user->img;
            $data['user_name']=$user->user;
        }

        return response()->json(['coment' => $comment], 200);
    }

    public function postComent(Request $request)
    {
        $s=new Comment();
        $s->id_user=$request->idUsu;
        $s->id_receta=$request->id_receta;
        $s->comentary=$request->comment;
        $s->save();
    }

    public function deleteRecipe(Request $request)
    {
        Recipes::where('id',$request->idRecipe)->delete();
    }

    public function updateRecipe(Request $request)
    {
//        $attributes=request()->validate([
//            'tit'=>'required|max:255|unique:recipes,name_recipe',
//        ]);

        $idAmount=[];

        $reci=Recipes::where('id',$request->recipeId)->first();

        $reci->name_recipe=$request->tit;
        $reci->img=$request->img;
        $reci->nacionality=$request->pais;
        $reci->description=$request->desc;
        $reci->save();

        /*
         * dentro del for con el count del array de nombres hacemos un if para que si existe el amount,
         * si no existe lo creamos, sino lo modificamos
         * */
        $amou=Amount::where('id_receta',$request->recipeId)->get();

        for($i=0;$i<count($request->nom);$i++)
        {
            echo $i." - ".(count($amou)-1)."  ";
            if($i<count($amou))
            {
                echo "hola";
                $amou[$i]->cantidad=$request->cant[$i];
                $idAmount[]=$amou[$i]->id;
                $amou[$i]->save();

                $ingre=Ingredient::where('id_amount',$amou[$i]->id)->first();
                $ingre->name_ingredient=$request->nom[$i];
                $ingre->id_category=$request->filt[$i];
                $ingre->save();
            }
            else
            {
                $s=new Amount();
                $s->id_receta=$request->recipeId;
                $s->cantidad=$request->cant[$i];
                $s->save();

                $idAmount[]=$s->id;

                $a=new Ingredient();
                $a->name_ingredient=$request->nom[$i];
                $a->id_category=$request->filt[$i];
                $a->id_amount=$idAmount[$i];
                $a->save();
            }
        }
    }

}
