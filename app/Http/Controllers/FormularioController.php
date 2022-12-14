<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FormularioController extends Controller
{
    /* Función de prueba */
    public function addCambioTurno (Request $request) {

        return $request;
    }

    public function getVerifications () {
        $query1 = "SELECT id_verificacion_tipo, tipo_verificacion, id_categoria_verificacion FROM entrega_turnos_verificacion_tipo";

        //Se ejecutan las mismas queries almacenándolas en una variable que contendrá una respuesta
        $result1 = DB::connection()->select(DB::raw($query1));

        $query_contador = "SELECT COUNT(id_verificacion_tipo) as cuenta FROM entrega_turnos_verificacion_tipo";

        $resultado_contador = DB::connection()->select(DB::raw($query_contador));

        //Se agrupan las respuestas en un JSON, agrupando las verificaciones con su respectiva clave.
        return response(json_encode([
            "formulario" => $result1,
            "contador" => $resultado_contador
        ]));
    }

    public function getCategories() {
        $query_categories = "SELECT * FROM entrega_turnos_categoria_verificacion";

        $result_cat = DB::connection()->select(DB::raw($query_categories));

        return $result_cat;
    }

    public function getResponses() {

        //Queries que toman las posibles respuestas conforme a su categoría de respuesta.
        $query1 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 1 AND estado = 1";

        //Resultado de las queries que se almacenan en una variable.
        $result1 = DB::connection()->select(DB::raw($query1));

        $query2 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 2 AND estado = 1";

        $result2 = DB::connection()->select(DB::raw($query2));

        $query3 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 3 AND estado = 1";

        $result3 = DB::connection()->select(DB::raw($query3));

        $query4 = "SELECT * FROM entrega_turnos_verificacion_estado WHERE id_categoria_respuesta = 4 AND estado = 1";

        $result4 = DB::connection()->select(DB::raw($query4));

        //Se agrupan en un JSON con una clave que contendrá todas las posibles respuestas.
        return response(json_encode([
            "no_corr" => $result1,
            "c_nc_na" => $result2,
            "b_r_m" => $result3,
            "s_a" => $result4
        ]));
    }

    public function insertIntoBitacora (Request $request) {

        /* Validaciones que comprueban si cada campo cumple con las debidas reglas descritas aquí abajo: */
        $fields = $request->validate([
            'id_verificacion_tipo' => 'regex:/^[0-9\s]*$/', //Comprueba si los caracteres especificados en la expresión regular coinciden con el valor
            'id_estado_verificacion' => 'regex:/^[0-9\s]*$/',
            'comentarios' => 'nullable|string', //Comprueba si es una cadena de texto
            'valor' => 'nullable|regex:/^[0-9\s]*$/'
        ]);

        /* Se recorre cada posición del objeto Request y se hace lo siguiente: */
        for ($index = 0; $index < 72; $index++) { 

            /* Por cada posición, hace una query para insertar */
            $query = "INSERT INTO entrega_turnos_verificacion_bitacora 
            (id_verificacion_tipo, id_estado_verificacion, comentarios, valor)
            VALUES (?, ?, ?, ?)";

            /* Si el índice existe, la query se ejecuta tomando todos los valores existentes en
               la posición actual */
            if (isset($request[$index])) {
                DB::connection()->select(DB::raw($query), 
                [$request[$index]["id_tipo_verificacion"], $request[$index]["id_estado_verificacion"], 
                $request[$index]["comentarios"], $request[$index]["valor"]]);
            }

            /* Si no existe, se devuelve un mensaje de error en conjunto con el índice que está fallando */
            else {
                echo("Error en el índice" . $index);
            }
        }
    }

    public function insertIntoMainBitacora (Request $request) {

        /* Validaciones que evalúan el valor en busca de coincidencias */
        $fields = $request->validate([
            'id_turno' => 'required|regex:/^[0-9\s]*$/', //Evalúa si la expresión regular coincide y además si es requerido
            'id_movil' => 'required|regex:/^[0-9\s]*$/',
            'movil' => 'required|string', //Valida si es una cadena de texto
            'placa' => 'required|string',
            'id_auxiliar' => 'required|regex:/^[0-9\s]*$/',
            'id_conductor' => 'required|regex:/^[0-9\s]*$/',
            'id_medico' => 'required|regex:/^[0-9\s]*$/',
            'danos_automotor' => 'required', // Valida si es requerido
            'foto_automotor' => 'nullable|string', //Valida si puede ser nulo y si es una cadena de texto
            'comentarios_recibido' => 'nullable|string'
        ]);

        /* Se efectúa una query de inserción */
        $query_insert = "INSERT INTO entrega_turnos_bitacora (id_turno, id_movil, movil, placa, id_auxiliar,
        id_conductor, id_medico, danos_automotor, foto_automotor, comentarios_conductor, 
        comentarios_auxiliar, comentarios_recibido)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        //Verifica si el string de base64 existe o no para empezar la operación
        if ($request->foto_automotor != null || $request->foto_automotor != "") {

             // Se almacena el string en base64 de la foto de la móvil
             $imagen = $request->foto_automotor;
     
             //Se reemplaza el identificador al inicio del texto por un string vacío
             $imagen = str_replace('data:image/png;base64', '', $imagen);
     
             //Se reemplaza ese espacio por un caracter
             $imagen = str_replace(' ', '+', $imagen);
     
             //Se almacena el nombre de la imagen, que es la placa de la móvil
             $imagen_nombre = $request->placa;
     
             //Se consigue la fecha actual
             $fecha_imagen = date("Y-m-d");
     
             /* Se almacena en una variable la propia ruta de la imagen, en dónde se va a almacenar y que nombre
                va a tener. */
             $imagen_ruta = 'danos_movil/' . $request->id_movil . '/' . $imagen_nombre . $fecha_imagen . '.png';
     
             // Se junta la ruta con la imagen ya decodificada, y se guarda en el almacenamiento
             Storage::put($imagen_ruta, base64_decode($imagen));
        }

        else {
            $imagen_ruta = null;
        }

        // Se ejecuta la query, tomando todos los valores de la petición y la ruta de la imagen
        DB::connection()->select(DB::raw($query_insert),
        [$request->id_turno, $request->id_movil, $request->movil, $request->placa, $request->id_auxiliar,
        $request->id_conductor, $request->id_medico, $request->danos_automotor, $imagen_ruta,
        $request->comentarios_conductor, $request->comentarios_auxiliar, $request->comentarios_recibido]);
    }
}
