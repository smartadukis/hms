<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * List of valid roles for system users.
     *
     * @var array
     */
    protected $roles = [
        'admin','doctor','nurse','receptionist','lab_staff','pharmacist','accountant','patient','staff'
    ];

    /**
     * Display a paginated list of users with optional search and role filter.
     *
     * @param Request $request The HTTP request containing optional search and role parameters.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply search filter if provided
        if ($q = $request->input('search')) {
            $query->where(function($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        // Apply role filter if provided
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Paginate results with query params appended for persistence
        $users = $query->orderBy('name')->paginate(15)->appends($request->only(['search','role']));

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roles,
        ]);
    }

    /**
     * Display user details in a modal fragment (used for AJAX requests).
     *
     * @param User $user The user to display.
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the edit form for a user (usually for role change).
     *
     * @param User $user The user to edit.
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = $this->roles;
        return view('admin.users.edit', compact('user','roles'));
    }

    /**
     * Update the user's role or other admin-modifiable attributes.
     *
     * @param Request $request The HTTP request containing updated role data.
     * @param User $user The user being updated.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validate role to ensure it is within allowed roles
        $request->validate([
            'role' => ['required', Rule::in($this->roles)],
        ]);

        $current = Auth::user();

        // Prevent admin from demoting themselves
        if ($current->id === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role. Ask another admin.');
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users.index')->with('success','User updated.');
    }

    /**
     * Delete a user account from the system.
     *
     * @param User $user The user to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $current = Auth::user();

        // Prevent self-deletion
        if ($user->id === $current->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
