<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $roles = [
        'admin','doctor','nurse','receptionist','lab_staff','pharmacist','accountant','patient','staff'
    ];

    /**
     * Display a paginated list of users with search & role filter.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($q = $request->input('search')) {
            $query->where(function($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')->paginate(15)->appends($request->only(['search','role']));

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roles,
        ]);
    }

    /**
     * Return a modal fragment that shows user details (for AJAX injection).
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Return a modal fragment that contains the edit form (change role).
     */
    public function edit(User $user)
    {
        $roles = $this->roles;
        return view('admin.users.edit', compact('user','roles'));
    }

    /**
     * Update the user's role (or other allowed admin-updatable fields).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in($this->roles)],
        ]);

        $current = Auth::user();

        // Prevent admin accidentally demoting themselves
        if ($current->id === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role. Ask another admin.');
        }

        $user->role = $request->role;

        $user->save();

        return redirect()->route('admin.users.index')->with('success','User updated.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $current = Auth::user();
        if ($user->id === $current->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
