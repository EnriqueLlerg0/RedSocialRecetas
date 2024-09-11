<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\DeviceRecordController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/subir',[\App\Http\Controllers\registerLoginController::class,'storeUsuario']);
Route::post('/checkUsu',[\App\Http\Controllers\registerLoginController::class,'checkUsuario']);
Route::post('/subirReceta',[\App\Http\Controllers\subirRecetaController::class,'subir']);
Route::post('/upload',[\App\Http\Controllers\subirRecetaController::class,'upload']);
Route::get('/allRecipes',[\App\Http\Controllers\recipesController::class,'getRecipes']);
Route::post('/putLike',[\App\Http\Controllers\recipesController::class,'setFavorite']);
Route::delete('/putLike',[\App\Http\Controllers\recipesController::class,'deleteFavorite']);
Route::get('/favoriteRecipes',[\App\Http\Controllers\favoriteController::class,'getRecipes']);
Route::get('/getRecipeData',[\App\Http\Controllers\recipeDataController::class,'getRecipe']);
Route::get('/getComent',[\App\Http\Controllers\recipeDataController::class,'getComent']);
Route::post('/postComent',[\App\Http\Controllers\recipeDataController::class,'postComent']);
Route::put('/actualizarImagen',[\App\Http\Controllers\userDataController::class,'updateImage']);
Route::put('/actualizarUsuario',[\App\Http\Controllers\userDataController::class,'updateUsername']);
Route::put('/actualizarEmail',[\App\Http\Controllers\userDataController::class,'updateEmail']);
Route::put('/actualizarContraseña',[\App\Http\Controllers\userDataController::class,'updateContraseña']);
Route::put('/actualizarContraseñaNoLogin',[\App\Http\Controllers\userDataController::class,'updateContraseñaNoData']);
Route::get('/allRecipesUser',[\App\Http\Controllers\userDataController::class,'getAllRecipeUser']);
Route::get('/allDataUser',[\App\Http\Controllers\userDataController::class,'getAllDataUser']);
Route::delete('/deleteRecipe',[\App\Http\Controllers\recipeDataController::class,'deleteRecipe']);
Route::put('/updateRecipe',[\App\Http\Controllers\recipeDataController::class,'updateRecipe']);
Route::get('/translations/{lang}', [\App\Http\Controllers\TranslationController::class,'getTranslations']);
Route::get('/getTranslations', [\App\Http\Controllers\TranslationController::class,'getTranslationsValue']);
Route::get('/allUser', [\App\Http\Controllers\adminController::class,'getAllUser']);

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'showContactForm']);
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'sendContactEmail']);


