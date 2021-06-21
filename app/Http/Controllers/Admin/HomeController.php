<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\LogTrait;
use App\Models\{
    Role,
    User,
    Notification
};
use Illuminate\Support\Facades\{
    Validator, 
    Hash
};

class HomeController extends Controller
{
    use LogTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title          = 'Vista principal';
        $notifications  = Notification::where('user_id', auth()->user()->id)
                                        ->where('status', true)
                                        ->get()
                                        ->toJson();

        return view('admin.index', compact('title', 'notifications'));
    }

    public function dataUserList() {
        $users = User::with('role', 'profile')->get();
        return $users->toJson();
    }

    public function allUsers() {
        $users = $this->dataUserList();
        $title = 'Lista de usuarios';

        // Log de eventos del usuario
        $this->registerLog('Consulta de lista de usuarios');

        return view('admin.users.index', compact('users', 'title'));
    }

    public function allRoles() {
        $roles = Role::all();
        return $roles->toJson();
    }

    public function changeStatus(User $user) {
        $user->update(['status' => !$user->status]);
        $status = $user->status == 1 ? 'Habilitado' : 'Deshabilitado';

        // Log de eventos del usuario
        $this->registerLog("Cambió de status al usuario [email => $user->email, id => $user->id, status => $status]");

        return 'true';
    }

    public function detailUser(User $user) {
        $user_detail = $user->load('profile');

        // Log de eventos del usuario
        $this->registerLog("Ver detalles del usuario [email => $user->email, id => $user->id]");

        return $user_detail->profile;
    }

    public function register(Request $request) {
        $role_id = Role::where('name', $request->role)->value('id');
        
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors(),
                'message'   => 'Revise los campos correspondientes ',
            ], 202);
        }
        
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role_id'   => $role_id,
            'password'  => Hash::make($request->password),
        ]);

        // Log de eventos del usuario
        $this->registerLog("Nuevo registro de usuario [email => $user->email, id => $user->id, rol_asignado => $request->role]");

        return response()->json([
            'success'   => true,
            'user'      => $user,
            'message'   => '¡Usuario registrado correctamente! ',
        ], 200);
    }

    public function update(User $user, Request $request) {
        $role_id = Role::where('name', $request->role)->value('id');

        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors(),
                'message'   => 'Revise los campos correspondientes ',
            ], 202);
        }
        
        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'role_id'   => $role_id,
            'password'  => Hash::make($request->password),
        ]);

        // Log de eventos del usuario
        $this->registerLog("Actualización de registro de usuario [email => $user->email, id => $user->id, rol_asignado => $request->role]");

        return response()->json([
            'success'   => true,
            'user'      => $user,
            'message'   => '¡Usuario actualizado correctamente! ',
        ], 200);
    }

    public function confirmNotification() {
        $user_id        = auth()->user()->id;
        $notifications  = Notification::where('user_id', $user_id)->get();
        
        foreach($notifications as $notification) {
            $notification->update([
                'status' => false
            ]);
        }
    }
}
