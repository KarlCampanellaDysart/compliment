<?php namespace App;

use Illuminate\Contracts\Auth\Guard; 
use Laravel\Socialite\Contracts\Factory as Socialite; 
use Illuminate\Http\Request;

use App\Repositories\UserRepository;

class AuthenticateUser {     

     private $socialite;
     private $auth;
     private $users;

     public function __construct(Socialite $socialite, Guard $auth, UserRepository $users) {   
        $this->socialite = $socialite;
        $this->users = $users;
        $this->auth = $auth;
    }

    public function execute($request, $listener) {
       if (!$request) return $this->getAuthorizationFirst("facebook");
       $user = $this->users->findByUserNameOrCreate($this->getSocialUser("facebook"));

       $this->auth->login($user, true);

       return $listener->userHasLoggedIn($user);
    }

    private function getAuthorizationFirst($provider) { 
        return $this->socialite->driver($provider)->redirect();
    }

    private function getSocialUser($provider) {
        return $this->socialite->driver($provider)->user();
    }
}