<?php namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class PagesController extends Controller
{

    public function rkbSend()
    {
        if (!Auth::check()) {
            return Redirect::route('home')->with('error', __('errors.needauth'));
        }

        $user = Auth::user();
        $rkb = $user->rakeback;

        if ($rkb < 0.01) {
            return Redirect::route('home')->with('error', __('errors.rmin') . ' - 0.01 AZN');
        }

        $user->update([
            'balance' => $user->balance + $rkb,
            'rakeback' => $user->rakeback - $rkb
        ]);

        return Redirect::route('home')->with('success', __('errors.rsuc'));
    }

    public function ref(Request $request)
    {
        $ref = User::where('referred_by', $request->user()->affiliate_id)->count();
        $lvl = 0;
        $perc = 5;
        $width = 0;
        $min = 0;
        $max = 0;
        $promo = $request->get('promo');
        $refs = User::where('referred_by', $request->user()->affiliate_id)->orderBy('id', 'desc')->paginate(30);

        return view('pages.ref', [
            'refs' => $refs,
            'ref' => $ref,
            'perc' => $perc,
            'width' => $width,
            'lvl' => $lvl,
            'promo' => $promo,
        ]);
    }

}



