<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\Ingredient;
use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class subirRecetaController extends Controller
{
    public function subir(Request $request)
    {
        $idAmount=[];

        $attributes=request()->validate([
            'tit'=>'unique:recipes,name_recipe'
        ]);


        $s=new Recipes();
        $s->id_user=$request->usuId;
        $s->name_recipe=$request->tit;
        $s->img=$request->img;
        $s->nacionality=$request->pais;
        $s->description=$request->desc;
        $s->me_gusta=0;
        $s->save();

        for($i=0;$i<count($request->cant);$i++)
        {
            $b=new Amount();
            $b->id_receta=$s->id;
            $b->cantidad=$request->cant[$i];
            $b->save();

            $idAmount[]=$b->id;
        }


        for($i=0;$i<count($request->nom);$i++)
        {
            $a=new Ingredient();
            $a->name_ingredient=$request->nom[$i];
            $a->id_category=$request->filt[$i];
            $a->id_amount=$idAmount[$i];
            $a->save();
        }
    }

    public function upload(Request $request)
    {
        // Validar el archivo de imagen
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Obtener el archivo
        $file = $request->file('image');

        // Definir el nombre del archivo
        $filename = time() . '_' . $file->getClientOriginalName();

        // Conectar al servidor FTP
        $ftpHost = env('FTP_HOST');
        $ftpUsername = env('FTP_USERNAME');
        $ftpPassword = env('FTP_PASSWORD');
        $ftpPort = env('FTP_PORT', 21);
        $ftpRoot = env('FTP_ROOT');

        $ftpConn = ftp_connect($ftpHost, $ftpPort);
        if (!$ftpConn) {
            return response()->json(['message' => 'Could not connect to FTP server'], 500);
        }

        $login = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
        if (!$login) {
            ftp_close($ftpConn);
            return response()->json(['message' => 'Could not login to FTP server'], 500);
        }

        // Subir el archivo al servidor FTP
        $filePath = $file->getPathname();
        $remoteFilePath = $ftpRoot . '/' . $filename;

        $upload = ftp_put($ftpConn, $remoteFilePath, $filePath, FTP_BINARY);
        ftp_close($ftpConn);

        if (!$upload) {
            return response()->json(['message' => 'Failed to upload file'], 500);
        }

        // Generar la URL del archivo subido
        $ftpUrl = env('FTP_URL');
        $url = $ftpUrl . '/' . $filename;

        return response()->json(['message' => 'File uploaded successfully', 'url' => $url]);
    }
}
