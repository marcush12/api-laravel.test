<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            echo "Index de CarController AUTENTICADO"; die();
        } else {
            echo "NÃO AUTENTICADO -> Index de CarController"; die();
        }

    }
}
