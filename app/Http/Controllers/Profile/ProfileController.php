<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Hash;
use Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function index()
    {

        $user = Auth::guard('web')->user();
        $title = $user->name . " :: Profile";
        $label = "Profile List";
        $users = User::where('id', $user->id)->first();
        return view('profile.edit', compact('users', 'title', 'label'));
    }
    public function update(Request $request, $id)
    {
        $user = Auth::guard('web')->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->messages()->all());
            Alert::html('Validation Error!', $errors, 'error');
            return redirect()->back()->withInput();
        }

        //path for photo
        $path_load = config('url.public_path');
        if ($request->hasFile('photo')) {
            $photo1 = $request->file('photo');
            $photo = "user" . rand(100, 999) . time() . '.' . $photo1->getClientOriginalExtension();
            $destinationPath = $path_load . 'user/';
            $photo1->move($destinationPath, $photo);
        } else {
            $userData = User::findOrFail($id);
            $photo = $userData->photo;
        }

        // Update User
        User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'photo' => $photo,
        ]);

        toast('Your Profile is now updated successfully.', 'success');
        return redirect()->route('profile.index');
    }
    public function verifyPassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect password!'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'core_password' => $user->core_password
        ]);
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'core_password' => $request->password
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully!'
        ]);
    }


}
