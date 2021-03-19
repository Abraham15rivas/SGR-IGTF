<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Profile, User};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index() {
        $user = auth()->user();
        $title = "Perfil de usuario $user->name";
        return view('profile.index', compact('title'));
    }

    public function store(Request $request) {
        if($request->ajax()) {
            $user = auth()->user();
            $profile = new Profile();
            $profile->first_name        = $request->first_name;
            $profile->second_name       = $request->second_name;
            $profile->surname           = $request->surname;
            $profile->second_surname    = $request->second_surname;
            $profile->user_id           = $user->id;
            
            if($request->hasFile('image')) {
                $path = $request->file('image')->store('public/profile/images');
                $profile->image = explode('public/', $path)[1];
            }
            $profile->save();

            return response()->json([
                'success' => true,
                'profile' => $profile,
                'message' => '¡perfil guardado correctamente! ',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No guardado algo salio mal! ',
            ], 400);
        }
    }

    public function show() {
        $user = auth()->user();
        $profile = Profile::where('user_id', $user->id)->first();
        if(!empty($profile)) {
            return response()->json($profile);
        } else {
            return null;
        }
    }

    public function update(Profile $profile, Request $request) {
        if($request->ajax()) {
            $user = auth()->user();
            if($request->hasFile('image')) {
                if($profile->image) {
                    Storage::disk('public')->delete($profile->image);
                }
                $path = $request->file('image')->store('public/profile/images');
                $request->image = explode('public/', $path)[1];
            }
            $profile->update([
                'first_name'        => $request->first_name,
                'second_name'       => $request->second_name,
                'surname'           => $request->surname,
                'second_surname'    => $request->second_surname,
                'image'             => $request->image,
                'user_id'           => $user->id
            ]);
            return response()->json([
                'success' => true,
                'profile' => $profile,
                'message' => '¡perfil actualizado correctamente! ',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No actualizo algo salio mal! ',
            ], 400);
        }
    }

    public function changePassword() {
        $user = auth()->user();
        $title = "Cambio de contraseña $user->name";
        return view('profile.change_password', compact('title'));
    }

    public function setPassword(User $user, Request $request) {
        if($request->ajax()) {   
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 200);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Contraseña actualizada correctamente! ',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No actualizo algo salio mal! ',
            ], 400);
        }
    }
}
