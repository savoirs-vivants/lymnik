<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Analyse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackofficeUserCreated;

class BackOfficeController extends Controller
{

    public function index()
    {
        $users = User::latest()->get();
        return view('desktop.backoffice.index', compact('users'));
    }

    public function showUser(int $id)
    {
        $user = User::findOrFail($id);

        $totalAnalyses = Analyse::where('user_id', $user->id)->where('est_valide', true)->count();
        $totalPoints = Analyse::where('user_id', $user->id)->where('est_valide', true)->distinct('point_id')->count('point_id');

        $qualiteData = Analyse::where('user_id', $user->id)
            ->where('est_valide', true)
            ->selectRaw('qualite, count(*) as total')
            ->groupBy('qualite')
            ->pluck('total', 'qualite');

        $typeData = Analyse::where('user_id', $user->id)
            ->where('est_valide', true)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $dernieresAnalyses = Analyse::with('point.coursDEau')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('desktop.backoffice.show', compact(
            'user', 'totalAnalyses', 'totalPoints', 'qualiteData', 'typeData', 'dernieresAnalyses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['nullable', 'string', 'in:admin,participant'],
        ]);

        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($plainPassword);

        if (empty($validated['role'])) {
            $validated['role'] = 'participant';
        }

        $user = User::create($validated);

        try {
            Mail::to($user->email)->send(new BackofficeUserCreated($user, $plainPassword));
        } catch (\Exception $e) {
            // Mail non bloquant
        }

        return back()->with('success', 'Le nouvel utilisateur a été créé et ses identifiants lui ont été envoyés par e-mail.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'      => ['nullable', 'string', 'in:admin,participant'],
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
