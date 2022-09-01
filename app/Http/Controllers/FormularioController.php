<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormularioController extends Controller
{
    public function addCambioTurno (Request $request) {

        /*$query1 = "INSERT INTO entrega_turnos-verificacion (kilometraje, estado_llantas_delanteras, 
        estado_llantas_traseras, estado_llantas_repuesto, soat, tecnicomecanica, botiquin, cascos_tripulacion,
        chaleco_tripulacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";*/

        return $request;
    }

    public function getVerifications () {

        $query1 = "SELECT id_verificacion_tipo, tipo_verificacion FROM entrega_turnos_verificacion_tipo WHERE
        id_categoria_verificacion = 1";

        $result1 = DB::connection()->select(DB::raw($query1));

        $query2 = "SELECT id_verificacion_tipo, tipo_verificacion FROM entrega_turnos_verificacion_tipo WHERE
        id_categoria_verificacion = 2";

        $result2 = DB::connection()->select(DB::raw($query2));

        $query3 = "SELECT id_verificacion_tipo, tipo_verificacion FROM entrega_turnos_verificacion_tipo WHERE
        id_categoria_verificacion = 3";

        $result3 = DB::connection()->select(DB::raw($query3));

        $query4 = "SELECT id_verificacion_tipo, tipo_verificacion FROM entrega_turnos_verificacion_tipo WHERE
        id_categoria_verificacion = 4";

        $result4 = DB::connection()->select(DB::raw($query4));

        return response(json_encode([
            "verificacion" => $result1,
            "estado_vehiculo" => $result2,
            "herramientas" => $result3,
            "equipos" => $result4
        ]));
    }

    public function getResponses() {

        $query1 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 1";

        $result1 = DB::connection()->select(DB::raw($query1));

        $query2 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 2";

        $result2 = DB::connection()->select(DB::raw($query2));

        $query3 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 3";

        $result3 = DB::connection()->select(DB::raw($query3));

        $query4 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 4";

        $result4 = DB::connection()->select(DB::raw($query4));

        return response(json_encode([
            "no_corr" => $result1,
            "c_nc_na" => $result2,
            "b_r_m" => $result3,
            "s_a" => $result4
        ]));
    }

    public function insertIntoBitacora (Request $request) {

        for ($index = 0; $index <= 61; $index++) { 
            $query = "INSERT INTO entrega_turnos_verificacion_bitacora 
            (id_verificacion_tipo, id_estado_verificacion, comentarios, valor)
            VALUES (?, ?, ?, ?)";

            if (isset($request[$index])) {
                DB::connection()->select(DB::raw($query), 
                [$request[$index]["id_tipo_verificacion"], $request[$index]["id_estado_verificacion"], 
                $request[$index]["comentarios"], $request[$index]["valor"]]);
            }

            else {
                echo("Error en el índice" . $index);
            }
        }
    }
}
