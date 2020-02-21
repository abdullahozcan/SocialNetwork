<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\User;
use Socialite;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function socialLiteLogin($provider)
    {
        // Socialite will pick response data automatic
        $socialUser = Socialite::driver($provider)->stateless()->user();

        //check if we already have a user
        $user=User::where("email", $socialUser->email)->first();
        if(!$user)
        {
          //register if not exists
          $data=[
              'name' => $socialUser->nickname,
              'email' => $socialUser->email,
              'password' => Hash::make(Str::random(60)),
              'api_token' => Str::random(60)
          ];

          if(!empty($socialUser->avatar))
          {
            $avatar = file_get_contents($socialUser->avatar);
            $filename = time().uniqid();
            $link=Storage::disk('public')->put($filename, $avatar);
            $url = Storage::url($filename);
            $data['avatar']=$url;

          }

          $user=User::create($data);
        }






        return response()->json([
             'user' => $user
         ]);
    }

    protected function authenticated(Request $request, $user)
    {


      return response()->json([
           'user' => $user
       ]);
    }




}
