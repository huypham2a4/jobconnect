<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RolePermission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $this->rememberPublicRedirect($request);

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

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectAfterAuth($request, $user)
                ->with('success', 'Đăng nhập thành công! Chào mừng '.$user->name);
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $this->rememberPublicRedirect($request);

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
            $rules['city'] = ['required', 'string', Rule::in(config('provinces'))];
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
            Company::create([
                'user_id' => $user->id,
                'name' => $validated['company_name'],
                'slug' => $this->uniqueSlug(Company::class, $validated['company_name'], null, 'cong-ty'),
                'city' => $validated['city'],
                'address' => $validated['address'] ?? null,
                'is_verified' => false,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterAuth($request, $user)
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất khỏi tài khoản.');
    }

    /**
     * Ghi nhớ trang công khai (tin tuyển dụng, công ty) mà khách đang xem trước khi bấm Đăng nhập/Đăng ký,
     * để sau khi đăng nhập quay lại đúng trang đó thay vì về trang tổng quan.
     */
    protected function rememberPublicRedirect(Request $request): void
    {
        $path = (string) $request->query('redirect', '');

        if ($this->isSafePublicPath($path)) {
            $request->session()->put('auth.redirect_after_login', $path);
        }
    }

    protected function isSafePublicPath(string $path): bool
    {
        return (bool) preg_match('#^/(jobs|companies)(/[A-Za-z0-9\-_]+)?/?(\?[A-Za-z0-9\-_=&%.+]*)?$#', $path);
    }

    protected function redirectAfterAuth(Request $request, User $user)
    {
        $path = (string) $request->session()->pull('auth.redirect_after_login', '');

        if ($this->isSafePublicPath($path)) {
            return redirect($path);
        }

        return $this->redirectBasedOnRole($user);
    }

    protected function redirectBasedOnRole(User $user)
    {
        $permissions = RolePermission::forRole($user->role);
        $routes = config('role_permissions.routes');
        foreach (array_keys(config('role_permissions.navigation.'.$user->role, [])) as $route) {
            if ($user->is_active && ($permissions[$routes[$route]] ?? false)) {
                return redirect()->route($route);
            }
        }

        return redirect()->route('home');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Định dạng email không hợp lệ.',
        ]);

        $user = User::where('email', $validated['email'])->first();
        // Cùng một thông báo cho mọi trường hợp để người lạ không dò được email nào đã đăng ký.
        $genericMessage = 'Nếu email tồn tại và tài khoản đang hoạt động, liên kết đặt lại mật khẩu đã được tạo (hiệu lực 60 phút).';

        if (! $user || ! $user->is_active) {
            return back()->with('success', $genericMessage);
        }

        $plainToken = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $plainToken, 'email' => $user->email]);

        $response = back()->with('success', $genericMessage);

        if (app()->environment('local', 'testing')) {
            $response->with('demo_reset_url', $resetUrl);
        }

        return $response;
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record || ! Hash::check($validated['token'], $record->token)) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'])->onlyInput('email');
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu đã hết hạn (quá 60 phút).'])->onlyInput('email');
        }

        $user = User::where('email', $validated['email'])->first();
        if (! $user || ! $user->is_active) {
            return back()->withErrors(['email' => 'Không thể đặt lại mật khẩu cho tài khoản này.'])->onlyInput('email');
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ]);

        $user = Auth::user();
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Đã cập nhật mật khẩu tài khoản thành công.');
    }
}
