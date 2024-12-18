<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $req)
    {
        $match = User::orderBy('created_at', 'desc')->where('role', '!=', 'business');

        $q = $req->query('q');

        if ($q) $match->where('firstNames', 'like', "%$q%")
            ->orWhere('lastNames', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%");

        $users = $match->paginate();

        return view('users.page', compact('users'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'firstNames' => 'required|string',
            'lastNames' => 'nullable|string',
            'role' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $alreadyEmail = User::where('email', $req->email)->first();

        if ($alreadyEmail) {
            return response()->json('Ya existe un usuario con el correo electrónico proporcionado', 400);
        }

        $user = new User();
        $user->firstNames = $req->firstNames;
        $user->lastNames = $req->lastNames;
        $user->role = $req->role;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->save();

        return response()->json('Usuario creado correctamente');
    }

    public function update(Request $req, $id)
    {
        $user = User::find($id);


        $req->validate([
            'firstNames' => 'required|string',
            'lastNames' => 'nullable|string',
            'role' => 'required|string',
            'email' => 'required|email',
        ]);

        $alreadyEmail = User::where('email', $req->email)->where('id', '!=', $user->id)->first();

        if ($alreadyEmail) {
            return response()->json('Ya existe un usuario con el correo electrónico proporcionado', 400);
        }

        $user->firstNames = $req->firstNames;
        $user->lastNames = $req->lastNames;
        $user->role = $req->role;
        $user->email = $req->email;
        $user->save();

        return response()->json('Usuario actualizado correctamente');
    }
    public function toggleStatus($id)
    {
        $user = User::find($id);
        $user->status = !$user->status;
        $user->save();

        $responseText = $user->status ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente';
        return response()->json($responseText);
    }

    public function resetPassword(Request $req, $id)
    {

        $randomPassword = substr(str_shuffle('aR0b3SZ45cefVWXghij78NOPKLMklmnopDEqrCFs12tuvTUYwxyzABGdHIJQ69'), 0, 8);

        $user = User::find($id);
        $user->password = bcrypt($randomPassword);
        $user->save();

        return response()->json('Contraseña restablecida: ' . $randomPassword);
    }
}
