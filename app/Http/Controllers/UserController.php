<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Receber post
        $json = $request->input('json', null);
        $params = json_decode($json);//convertendo para um objeto utilizável em php

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if (!is_null($email) && !is_null($password) && !is_null($name)) {
            //criar o usuário
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;
            //verificar se já exite um usuário no BD
            $isset_user = User::where('email', '=', $email)->first();
            $pkCount = (is_array($isset_user) ? count($isset_user) : 0);
            if ($pkCount == 0) {
                //salvar o usuário
                $user->save();
                $data = [
                    'status'=>'success',
                    'code'=>200,
                    'message'=> 'Usuário foi registrado com sucesso.'
                ];
            } else {
                //não salvar pq já existe
                $data = [
                'status'=>'error',
                'code'=>400,
                'message'=> 'Usuário já existe. Use outro email ou faça login.'
                ];
            }


        } else {
            $data = [
                'status'=>'error',
                'code'=>400,
                'message'=> 'Usuário não foi registrado.'
            ];
        }
        return response()->json($data, 200);
    }
    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();
        //receber POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
        //encriptar a senha
        $pwd = hash('sha256', $password);
        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
            $signup = $jwtAuth->signup($email, $pwd);//c o true devolve o obj decodificado
        } elseif ($getToken != null) {
            //var_dump($getToken); die();
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
        } else {
            $signup = [
                'status'=>'error',
                'message'=>'Envie seus dados por post.'
            ];
        }
        return response()->json($signup, 200);
    }

}
