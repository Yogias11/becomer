<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $success = 200;
    protected $error = 401;

    function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();

            $data['user'] = $user->createToken('secret')->accessToken;
            $data['id'] = $user->id;
            $data['email'] = $user->email;
        }

        return response()->json($data, $this->success, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->error, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $user = new User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $data['user'] = $user->createToken('secret')->accessToken;
        $data['id'] = $user->id;
        $data['email'] = $user->email;

        return response()->json($data, $this->success, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
