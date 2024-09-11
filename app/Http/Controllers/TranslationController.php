<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class TranslationController extends Controller
{
    public function getTranslations($lang)
    {
        App::setLocale($lang);
        $translations = Lang::get('messages');
        return response()->json($translations);
    }

    public function getTranslationsValue()
    {
        $dato=App::getLocale();
        return response()->json($dato);
    }
}
