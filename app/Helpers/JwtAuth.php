<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {
    public $key;//chave secreta para token
    public function __construct() {
        $this->key = 'minha-chave-secreta-123456789';
    }
    public function signup($email, $password, $getToken = null)
    {
        $user = User::where([//verificar se user existe
            'email'=>$email,
            'password'=>$password
        ])->first();
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        if ($signup) {
            //generate token e devolvê-lo
            $token = [
                'sub'=> $user->id,
                'email'=>$user->email,
                'name'=>$user->name,
                'surname'=>$user->surname,
                'iat'=> time(),
                'exp'=> time() + (7*24*60*60)//1 semana para expirar a partir de agora
            ];
            //token criado abaixo
            $jwt = JWT::encode($token, $this->key, 'HS256');//codifica o obj e converte em json objeto; passa chave secreta; algoritmo de codific
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if (is_null($getToken)) {
                return $jwt;
            } else {
                return $decoded;
            }
        } else {
            //devolver um erro
            return [
                'status'=> 'error',
                'message'=> 'Login pifou...'
            ];
        }
    }
    public function checkToken($jwt, $getIdentity = false)//receber token e verificar sua autenticidade
    {
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch(\UnexpectedValueException $e) {
            $auth = false;
        } catch(\DomainException $e) {
            $auth = false;
        }
        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
        if ($getIdentity) {
            return $decoded; //devolve o objeto do user identificado
        }
        return $auth;
    }
}

