<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\User;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function profil()
    {
        $user    = Auth::user();
        $isAdmin = $user->role === 'admin';

        $stats = [
            'analyses' => Analyse::where('user_id', $user->id)->count(),
            'points'   => Point::whereHas('analyses', fn($q) => $q->where('user_id', $user->id))->count(),
        ];

        return view('profil', compact('stats', 'user', 'isAdmin'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profil-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'firstname' => $request->firstname,
            'name'      => $request->name,
            'email'     => $request->email,
        ]);

        return redirect()->route('profil.edit')->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withInput()
                ->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profil.edit')
            ->with('success_password', 'Mot de passe modifié avec succès.')
            ->with('tab', 'password');
    }
}
