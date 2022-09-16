<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login (Request $request) {

        $query_busqueda1 = "SELECT Cod_Con, documento FROM conductores WHERE documento = ? AND Estado = 1";

        $result1 =  DB::connection()->select(DB::raw($query_busqueda1), [$request->documento]);

        if (count($result1) == 0) {
            $query_busqueda2 = "SELECT Cod_Aux, documento FROM auxiliares WHERE documento = ? AND Estado = 1";

            $result2 = DB::connection()->select(DB::raw($query_busqueda2), [$request->documento]);

            $token = $result2->createToken('apitoken')->plainTextToken;

            return $response = [
                'datos_usuario' => $result2,
                'token' => $token
            ];
        }

        else {
            $token = $result2->createToken('apitoken')->plainTextToken;

            return $response = [
                'datos_usuario' => $result1,
                'token' => $token
            ];
        }
    }
}
