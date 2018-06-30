<?php

namespace App\Http\Controllers;

use App\Car;
use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all()->load('user');
        return response()->json(array(
            'cars'=>$cars,
            'status'=>'success'
        ), 200);
    }
    public function show($id)
    {
        $car = Car::find($id)->load('user');
        return response()->json(array('car'=>$car, 'status'=>'success'), 200);
    }
    public function store(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            //Receber dados por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            //Pegar o usuário identificado
            $user = $jwtAuth->checkToken($hash, true);
            //validação

            $validate=\Validator::make($params_array, [
                'title' => 'required | min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }



            //salvar carro; dados serão passados via post (params)
            $car = new Car();
            $car->user_id = $user->sub;//id está guardado aqui
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(
                'car'=>$car,
                'status'=>'success',
                'code'=>200,
            );
        } else {
            //return error
            $data = array(
                'message'=>'Login incorreto!',
                'status'=>'error',
                'code'=>300,
            );
        }
        return response()->json($data, 200);
    }
    public function update(Request $request, $id)
    {//erro nesta função
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            //receber params que chegam por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            //validação

            $validate=\Validator::make($params_array, [
                'title' => 'required | min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }
            //atualizar o registro do carro
            $car = Car::where('id', $id)->update($params_array);
            $data = array(
                'car'=> $params,
                'status'=> 'success',
                'code'=> 200
            );
        } else {
            //return error
            $data = array(
                'message'=>'Login incorreto!',
                'status'=>'error',
                'code'=>300,
            );
        }
        return response()->json($data, 200);
    }
    public function destroy(Request $request, $id)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if ($checkToken) {
            $car = Car::find($id);
            $car->delete();
            $data = array(
                'car'=>$car,
                'status'=>'success',
                'code'=> 200
            );
        } else {
            $data = array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Login incorreto!'
            );
        }
        return response()->json($data, 200);
    }
}
