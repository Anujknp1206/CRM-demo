<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        $title = "Admin :: Login Page";

        $demoUser = null;

        try {

            $demoUser = User::where('email', 'test@crmsystem.com')->first();

        } catch (\Throwable $e) {

            // Database may be resetting.
        }

        return view('auth.index', compact(
            'title',
            'demoUser'
        ));
    }
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img('math')]);
    }
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
                'captcha' => 'required|captcha',
            ],
            [
                'email.required' => 'Email cannot be empty.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Password cannot be blank.',
                'captcha.required' => 'Captcha is required.',
                'captcha.captcha' => 'Captcha is incorrect.',
            ]
        );

        if ($validator->fails()) {

            $errors = implode('<br>', $validator->messages()->all());

            Alert::html('Validation Error!', $errors, 'error');

            return redirect()->back()->withInput();
        }

        // Remember Me
        $remember = $request->boolean('remember');

        // Attempt Login
        if (
            Auth::guard('web')->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                    'is_active' => true,
                ],
                $remember
            )
        ) {

            $request->session()->regenerate();

            session()->flash('demo_notice', true);

            return redirect()->intended(route('dashboard'));
        }

        // Authentication Failed
        toast('Invalid email or password.', 'error');

        return redirect()->back()->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        toast('Logout Successfully..!', 'success');
        return redirect()->route('login');
    }
}
