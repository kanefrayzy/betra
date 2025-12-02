<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\AuthService;
use App\Services\User\ExternalAuthService;
use App\Services\User\RegistrationService;
use App\Services\User\UsernameGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class SocialLoginController extends Controller
{

    public function __construct(
        protected RegistrationService $registrationService,
        protected AuthService         $authService,
        protected ExternalAuthService $externalAuthService,
        protected UsernameGeneratorService $usernameGenerator
    )
    {
    }

    public function handler(Request $request): RedirectResponse
    {
        $token = $request->input('token');
        $data = $this->externalAuthService->getUserData($token);

        if (!$data || !property_exists($data, 'identity') || !property_exists($data, 'network')) {
            return Redirect::back()->with('error', __('errors.er'));
        }

        $user = User::where('network_id', $data->identity)
            ->where('network_type', $data->network)
            ->first();

        if ($user) {
          if ($user->ban) {
              Auth::logout();
              return back();
          }
            if ($this->authService->uLogin($user)) {
                Session::flash('success', __('errors.sucauth'));
                return Redirect::intended(route('home'));
            } else {
                return Redirect::back()->with('error', __('errors.er'));
            }
        }


        $fullName = trim("{$data->first_name} {$data->last_name}");
        $username = $this->usernameGenerator->generate($fullName);
        
        // Сохраняем данные в сессию и редиректим на выбор валюты
        Session::put('social_auth_data', [
            'username' => $username,
            'network_id' => $data->identity,
            'network_type' => $data->network,
            'full_name' => $fullName,
        ]);
        
        return Redirect::route('home')->with('show_currency_modal', true);
    }
    
    /**
     * Завершить регистрацию через соцсети с выбранной валютой
     */
    public function completeSocialRegistration(Request $request): JsonResponse
    {
        $request->validate([
            'currency' => ['required', 'string', 'in:USD,RUB,KZT,TRY,AZN,UZS,EUR,PLN']
        ]);
        
        $socialData = Session::get('social_auth_data');
        
        if (!$socialData) {
            return response()->json([
                'success' => false,
                'message' => __('Сессия истекла. Попробуйте войти снова.')
            ], 400);
        }
        
        $registered = $this->registrationService->register([
            'username' => $socialData['username'],
            'network_id' => $socialData['network_id'],
            'network_type' => $socialData['network_type'],
            'currency' => $request->input('currency'),
        ]);

        if ($registered) {
            // Очищаем сессию
            Session::forget(['social_auth_data', 'show_currency_modal']);
            
            return response()->json([
                'success' => true,
                'message' => __('errors.sucreg'),
                'redirect' => route('home')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('errors.er')
        ], 400);
    }
}
