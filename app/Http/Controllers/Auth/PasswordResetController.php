<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPasswordMail;
use App\Mail\PasswordResetLinkMail;
use App\Repositories\UserRepository;
use App\Models\PasswordReset;
use App\Models\User;

class PasswordResetController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function resetPassword(Request $request) : RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('home')->with(['error' => __('errors.user_not_found')]);
        }

        $token = Str::random(60);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['user_id' => $user->id, 'token' => $token, 'expires_at' => now()->addHour()]
        );

        $resetLink = route('password.recovery', ['token' => $token, 'email' => $request->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetLinkMail($resetLink));
        } catch (\Exception $e) {
            \Log::error(__('Ошибка при отправке письма с ссылкой для сброса пароля.'), ['email' => $user->email, 'error' => $e->getMessage()]);
            return redirect()->route('home')->with('error', __('errors.er'));
        }

        return redirect()->route('home')->with('success', __('home.password_reset_link_sent'));
    }

    public function resetPasswordWithToken(Request $request) : RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'size:60'],
            'email' => 'required|email'
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('home')->with(['error' => __('errors.invalid_or_expired_link')]);
        }

        $user = User::find($reset->user_id);

        if (!$user) {
            return redirect()->route('home')->with(['error' => __('errors.user_not_found')]);
        }

        $newPassword = Str::random(10);
        $this->userRepository->updatePassword($user, $newPassword);

        try {
            Mail::to($user->email)->send(new NewPasswordMail($newPassword));
        } catch (\Exception $e) {
            \Log::error(__('Ошибка при отправке письма с новым паролем.'), ['email' => $user->email, 'error' => $e->getMessage()]);
            return redirect()->route('home')->with('error', __('errors.er'));
        }

        PasswordReset::where('email', $request->email)->delete();

        return redirect()->route('home')->with('success', __('home.new_password_sent'));
    }
}
