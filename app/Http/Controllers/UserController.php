<?php

namespace App\Http\Controllers;

use App\Helpers\APIHelper;
use App\Helpers\UserHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Has;

use Image;
use MongoDB\BSON\ObjectId;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = UserHelper::hierarchy(session()->get('selectedUser')->_id);

        return response()->json(['users' => $users], 200);

    }

    public function show($id)
    {
        return response()->json(['user' => User::where('_id', $id)->first()->toArray()], 200);
    }

    public function updateData($id, Request $request)
    {
        $inputs = $request->only(['firstName', 'lastName', 'email', 'phone', 'gender']);
        $errors = [];

        //compruebo si existe un usuario con el email que no sea el propio usuario
        $userDuplicated = User::where('email', $inputs['email'])->first();
        if (!is_null($userDuplicated) && $userDuplicated->_id != $id) {
            $code = '001x002';
            $errors['email'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // email vacío
        if ($inputs['email'] == '') {
            $code = '000x001';
            $errors['email'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // nombre vacío
        if ($inputs['firstName'] == '') {
            $code = '000x001';
            $errors['firstName'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // apellido vacío
        if ($inputs['lastName'] == '') {
            $code = '000x001';
            $errors['lastName'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // teléfono valido
        if (isset($inputs['phone']) && !APIHelper::validatePhone($inputs['phone'])) {
            $code = '001x004';
            $errors['phone'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //compruebo si existe un usuario con el mismo teléfono
        if (isset($inputs['phone']) && $inputs['phone']) {
            $userDuplicatedPhone = User::where('phone', $inputs['phone'])->first();
            if (!is_null($userDuplicatedPhone) && $userDuplicatedPhone->_id != $id) {
                $code = '001x007';
                $errors['phone'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
            }
        }


        if (count($errors) > 0)
            return response()->json(['errors' => $errors], 400);

        $user = User::where('_id', $id)->first();
        foreach ($inputs as $column => $value)
            $user->{$column} = $value;

        $user->save();
        return response()->json(['user' => $user], 200);
    }

    public function updateImage($id, Request $request)
    {
        $user = User::where('_id', $id)->first();


        $file = $request->file('file');
        $fileName = sprintf("profile_%s_%s.jpg", Carbon::now()->timestamp, $user->_id);

        $image = Image::make($file)->orientate();

        //logica de recorte
        $size = min($image->width(), $image->height());
        $position = (object)['x' => 0, 'y' => 0];
        $margin = abs($image->width() - $image->height()) / 2;
        if ($image->width() > $image->height()) $position->x = $margin; else $position->y = $margin;
        $image->crop($size, $size, $position->x, $position->y);

        if ($user->profileImage != 'default.jpg')
            Storage::disk('profile')->delete($user->profileImage);

        Storage::disk('profile')->put($fileName, $image->encode());

        $user->profileImage = $fileName;
        $user->save();

        return response()->json(['user' => $user], 200);


//        return response()->json(['message' => 'No tienes permiso para esto...'], 503);

    }

    public function updateBill($id, Request $request)
    {
        $inputs = $request->only(['address', 'province', 'town', 'postal', 'dni']);
        $errors = [];

        if ($inputs['dni'] && !APIHelper::validateDNI($inputs['dni'])) {
            $code = '001x003';
            $errors['dni'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        if ($inputs['postal'] && !APIHelper::validatePostal($inputs['postal'])) {
            $code = '001x005';
            $errors['postal'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        if (count($errors) > 0)
            return response()->json(['errors' => $errors], 400);

        $user = User::where('_id', $id)->first();
        foreach ($inputs as $column => $value)
            $user->{$column} = $value;

        $user->save();
        return response()->json(['user' => $user], 200);
    }

    //verificación
    public function updateVerification($id)
    {
        $user = User::where('_id', $id)->first();

        if (!$user->verification_code) {
            $user->verification_code = random_int(10000, 99999);
            $user->save();
        }

        $code = $user->verification_code;

        $message = APIHelper::getWhatsAppMessage('verification', ['name' => $user->firstName, 'code' => $code]);
        APIHelper::sendWhatsapp('+34' . $user->phone, $message);

        return response()->json(['message' => 'El código ha sido generado y enviado'], 200);
    }

    public function verify($id, Request $request)
    {
        $user = User::where('_id', $id)->first();
        $verificationCode = $request->only(['verification']);

        if ($user->verification_code == $verificationCode['verification']) {
            $user->accountVerifiedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
            return response()->json(['message' => 'Tu cuenta ha sido verificada'], 200);
        }

        $code = '001x006';
        $errors['verification'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];

        return response()->json(['errors' => $errors], 400);

    }

    //cambio de contraseña
    public function updatePassword($id, Request $request)
    {

        $inputs = $request->only(['current_password', 'password', 'password_confirmation']);
        $errors = [];

        // contraseña actual vacía
        if (isset($inputs['current_password']) && $inputs['current_password'] == '') {
            $code = '000x001';
            $errors['current_password'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // nueva contraseña vacía
        if ($inputs['password'] == '') {
            $code = '000x001';
            $errors['password'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // contraseña confirmación vacía
        if ($inputs['password_confirmation'] == '') {
            $code = '000x001';
            $errors['password_confirmation'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        $user = User::where('_id', $id)->first();

        //coincide contraseña actual
        if (isset($inputs['current_password']) && !Hash::check($inputs['current_password'], $user->password)) {
            $code = '001x001';
            $errors['current_password'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //contraseña cumple requisitos
        if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $inputs['password'])) {
            $code = '000x002';
            $errors['password'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //contraseñas coinciden
        if ($inputs['password'] !== $inputs['password_confirmation']) {
            $code = '000x003';
            $errors['password_confirmation'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        if (count($errors) > 0)
            return response()->json(['errors' => $errors], 400);

        $user->password = Hash::make($inputs['password']);
        $user->save();

        return response()->json(['message' => 'La contraseña ha sido actualizada'], 200);
    }

    public function store(Request $request)
    {
        $requiredFields = ['firstName', 'lastName', 'password', 'password_confirmation', 'email', 'users'];
        $required = $request->only($requiredFields);
        $optional = $request->except($requiredFields);

        $errors = [];

        unset($optional['errors']);

        //compruebo si existe un usuario con el mismo email
        $userDuplicatedEmail = User::where('email', $required['email'])->first();
        if (!is_null($userDuplicatedEmail)) {
            $code = '001x002';
            $errors['email'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //compruebo si existe un usuario con el mismo teléfono
        if (isset($optional['phone']) && $optional['phone']) {
            $userDuplicatedPhone = User::where('phone', $optional['phone'])->first();
            if (!is_null($userDuplicatedPhone)) {
                $code = '001x007';
                $errors['phone'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
            }
        }

        // email vacío
        if ($required['email'] == '') {
            $code = '000x001';
            $errors['email'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // nombre vacío
        if ($required['firstName'] == '') {
            $code = '000x001';
            $errors['firstName'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // apellido vacío
        if ($required['lastName'] == '') {
            $code = '000x001';
            $errors['lastName'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        // teléfono valido
        if (isset($required['phone']) && !APIHelper::validatePhone($required['phone'])) {
            $code = '001x004';
            $errors['phone'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //contraseña cumple requisitos
        if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $required['password'])) {
            $code = '000x002';
            $errors['password'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        //contraseñas coinciden
        if ($required['password'] !== $required['password_confirmation']) {
            $code = '000x003';
            $errors['password_confirmation'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        if (count($required['users']) < 1) {
            $code = '000x001';
            $errors['users'] = ['code' => $code, 'message' => APIHelper::getResponseByCode($code)];
        }

        if (count($errors) > 0)
            return response()->json(['errors' => $errors], 400);


        $userCreated = User::insert([
            'firstName' => $required['firstName'],
            'lastName' => $required['lastName'],
            'gender' => $optional['gender'],
            'accountVerifiedAt' => '',
            'profileImage' => 'default.jpg',
            'isActive' => true,
            'label' => $optional['label'],
            'email' => $required['email'],
            'phone' => $optional['phone'],
            'password' => Hash::make($required['password']),
            'province' => '',
            'town' => '',
            'address' => '',
            'postal' => '',
            'dni' => '',
            'responsible' => $required['users'],
            'verification_code' => '',
            'devices' => [],
            'favDevices' => [],
            'favRoutes' => [],
            'createdAt' => Carbon::now()->timestamp
        ]);

        return response()->json(['message' => 'El usuario ha sido registrado en la aplicación', 'user' => $userCreated], 200);
    }

    public function select($id)
    {
        $user = session()->get('loggedUser');

        if ($id != 0)
            $user = User::where('_id', $id)->first();

        session()->put('selectedUser', $user);
        $user['selectedUser'] = session()->get('selectedUser');

        return response()->json(['message' => 'Se ha seleccionado un usuario'], 200);
    }

    public function toggleFav($id, Request $request) {
//        dd($id, $request->all());

        $user = User::where('_id', $id)->first();

        $routes = $user->favRoutes;

        $keyRoute = array_search($request['route'], $routes);

        if ($keyRoute !== false)
            unset($routes[$keyRoute]);
        else
            $routes[] = $request['route'];

        $user->favRoutes = array_values($routes);
        $user->save();

        return response()->json(['message' => 'La ruta ha sido añadida a favoritos'], 200);

    }


    public function logout()
    {
        session()->flush();
    }
}
