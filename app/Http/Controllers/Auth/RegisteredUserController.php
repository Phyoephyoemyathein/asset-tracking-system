<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
{
    // ဥပမာ - Admin မဟုတ်ရင် သို့မဟုတ် Login မဝင်ရသေးရင်
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login')->with('error', 'You do not have permission to register, pls contact to admin');
    }

    return view('auth.register');
}
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,staff'], // 👈 Admin က role သတ်မှတ်ပေးရန် (admin သို့မဟုတ် staff)
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // 👈 Database ထဲသို့ role ထည့်သွင်းခြင်း
        ]);

        event(new Registered($user));

        // 🚫 Admin က register လုပ်ပေးတာဖြစ်လို့ အသစ်စက်စက် user အကောင့်နဲ့ Auto Login ဝင်စရာမလိုပါ
        // Auth::login($user); 

        // 🔄 Admin Dashboard ဆီသို့ ပုံမှန်အတိုင်း ပြန်လည်ပို့ဆောင်ပေးခြင်း (Success message နဲ့အတူ)
        return redirect()->back()->with('success', 'Staff account created successfully!');
    }
}
