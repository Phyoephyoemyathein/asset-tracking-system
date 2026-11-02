<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 👤 Admin မှ Staff/User စာရင်းအားလုံးကို ကြည့်ရှုခြင်း
    public function index()
    {
        // ကိုယ့်အကောင့်မှလွဲ၍ ကျန် Users များကို ထုတ်ပြရန် (သို့မဟုတ် အားလုံးပြရန်)
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 🗑️ User တစ်ဦးအား ဖျက်သိမ်းခြင်း
    public function destroy(User $user)
    {
        // Admin ကိုယ့်အကောင့် ကိုယ်ပြန်မဖျက်မိစေရန် ကာကွယ်ခြင်း
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account!');
        }

        $user->delete();

        return back()->with('success', 'User account deleted successfully!');
    }

    public function edit(User $user)
{
    return view('admin.users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,staff',
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
}

public function __construct()
{
    // Admin မှလွဲ၍ Staff များ ဝင်ရောက်ခွင့်မရှိစေရန်
    if (auth()->check() && strtolower(trim(auth()->user()->role)) !== 'admin') {
        abort(403, 'Unauthorized action.');
    }
}

}
