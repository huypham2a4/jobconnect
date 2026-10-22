<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:candidate,employer'],
        ];

        if ($request->role === 'employer') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['city'] = ['required', 'string', 'max:100'];
            $rules['address'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng trên hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
            'company_name.required' => 'Vui lòng nhập tên công ty/doanh nghiệp.',
            'city.required' => 'Vui lòng chọn tỉnh/thành phố đặt trụ sở.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        if ($user->role === 'employer') {
            $slug = Str::slug($validated['company_name']);
            // Ensure unique slug
            $existingCount = Company::where('slug', 'like', $slug . '%')->count();
            if ($existingCount > 0) {
                $slug .= '-' . ($existingCount + 1);
            }

            Company::create([
                'user_id' => $user->id,
                'name' => $validated['company_name'],
                'slug' => $slug,
                'city' => $validated['city'],
                'address' => $validated['address'] ?? null,
                'is_verified' => false,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất khỏi tài khoản.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employer' => redirect()->route('employer.dashboard'),
            default => redirect()->route('candidate.dashboard'),
        };
    }
}
