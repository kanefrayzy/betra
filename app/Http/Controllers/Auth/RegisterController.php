<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\User\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function __construct(protected RegistrationService $service) {}

    public function register(RegisterRequest $request): RedirectResponse|JsonResponse
    {
        $registered = $this->service->register($request->only(['username', 'email', 'password', 'currency']));

        if ($registered) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Аккаунт успешно создан'),
                    'redirect' => route('home')
                ]);
            }
            
            Session::flash('success', __('Аккаунт успешно создан'));
            return Redirect::intended();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('Произошла неизвестная ошибка')
            ], 500);
        }

        return Redirect::back()->withErrors(__('Произошла неизвестная ошибка'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email']
        ], [
            'email.required' => __('errors.inem'),
            'email.email' => __('errors.badem'),
            'email.exists' => __('errors.noem'),
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $code = $this->generateResetCode();

        try {
            $this->createPasswordReset($user, $code);
            $this->sendResetEmail($user, $code);

            return response()->json(['msg' => trans('errors.sencod')], HttpStatus::CREATED);
        } catch (\Throwable $th) {
            return response()->json(['error' => __('errors.er') . $th->getMessage()], HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function password(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed'],
            'token' => ['required', 'min:6']
        ], [
            'email.required' => __('errors.inem'),
            'email.email' => __('errors.badem'),
            'email.exists' => __('errors.noem'),
            'password.required' => __('errors.inpass'),
            'password.min' => __('errors.minpass'),
            'password.confirmed' => __('errors.nesov'),
            'token.required' => __('errors.nocod'),
            'token.min' => __('errors.mincod'),
        ]);

        $user = $this->getUserFromResetToken($request->email, $request->token);

        if (!$user) {
            return response()->json(['error' => __('errors.istek')], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Auth::login($user);
        Session::flash('success', __('errors.sucpas'));

        return Response::json(['success' => true], HttpStatus::CREATED);
    }

    private function generateResetCode(): int
    {
        return rand(1000000, 9999999);
    }

    private function createPasswordReset(User $user, int $code): void
    {
        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $code,
            'expires_at' => now()->addHour()
        ]);
    }

    private function sendResetEmail(User $user, int $code): void
    {
        Mail::send('emails.password-reset', ['user' => $user, 'code' => $code], function ($message) use ($user) {
            $message->to($user->email, $user->username)
                    ->subject(__('errors.vost'));
        });

        if (Mail::failures()) {
            throw new \Exception(__('errors.er'));
        }
    }

    private function getUserFromResetToken(string $email, string $token): ?User
    {
        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        return $reset ? User::find($reset->user_id) : null;
    }
}
