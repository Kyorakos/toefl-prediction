<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/student/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
            'registration_code' => ['required', 'string', 'exists:registration_codes,code'],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $registrationCode = RegistrationCode::where('code', $request->registration_code)->first();
        
        if (!$registrationCode) {
            return back()->withErrors(['registration_code' => 'Invalid registration code.']);
        }

        if ($registrationCode->is_used) {
            return back()->withErrors(['registration_code' => 'Registration code has already been used.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Plain text as requested
            'registration_code' => $request->registration_code,
        ]);

        $registrationCode->update([
            'is_used' => true,
            'used_by' => $user->id,
            'used_at' => now(),
        ]);

        auth()->login($user);

        return redirect($this->redirectTo);
    }
}
