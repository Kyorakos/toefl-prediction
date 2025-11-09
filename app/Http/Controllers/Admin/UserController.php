<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student');
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('registration_code', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $users = $query->with('registrationCode.batch')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function show(User $user)
    {
        $user->load(['registrationCode.batch', 'examSessions.batch']);
        return view('admin.users.show', compact('user'));
    }
    
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(8);
        $user->password = $newPassword;
        $user->save();
        
        return back()->with('success', 'Password has been reset to: ' . $newPassword);
    }
    
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin user');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully');
    }
}
