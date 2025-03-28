<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Mail\User\PasswordMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $password = Str::random(8);
        $data['password'] = Hash::make($password);


        unset($data['password_confirmation']);
        $user = User::firstOrCreate([
            'email' => $data['email'],
        ], $data);

        Mail::to($data['email'])->send(new PasswordMail($password));

        event(new Registered($user));

        return redirect()->route('admin.user.index');
    }
}
