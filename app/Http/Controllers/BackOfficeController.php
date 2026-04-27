<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BackOfficeController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('desktop.backoffice', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'      => ['nullable', 'string', 'in:admin,moderateur,utilisateur'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }
}
