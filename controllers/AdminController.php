<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {

    public static function index( Router $router ) {
        
        session_start();

        isAdmin();
        
        // Recojemos la fecha que ha escogido el usuario, si no hay fecha en la ruta GET significa que es la primera busqueda y buscamos sobre el dia actual
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        // Separamos año, mes y dia
        $fechas = explode('-', $fecha);

        // Revisamos que sea una fecha valida
        if( !checkdate( $fechas[1], $fechas[2], $fechas[0])) {
            // al no ser una vecha valida lo redireccionamos para que no este buscando algo que no va a encontrar
            header('Location: /404');
        }


        // Consultar la base de datos
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasServicios ";
        $consulta .= " ON citasServicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasServicios.servicioId ";
        $consulta .= " WHERE fecha =  '${fecha}' ";

        $citas = AdminCita::SQL($consulta);

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }

}


// SELECT * FROM citas 
// LEFT OUTER JOIN usuarios 
// ON citas.usuarioId=usuarios.id 
// LEFT OUTER JOIN citasservicios 
// ON citasservicios.citaId = citas.id
// LEFT OUTER JOIN servicios
// ON citasservicios.servicioId = servicios.id; // Une todas las tablas

// CONCAT(citas.fecha, ' ', citas.hora) as cita // ejemplo para unir campos en una tabla "virtual"


// Ejemplo de aspectos necesarios

// SELECT citas.id, 
// citas.hora, 
// CONCAT(usuarios.nombre, ' ', usuarios.apellido) as cliente,
// usuarios.email,
// usuarios.telefono,
// servicios.nombre as servicio,
// servicios.precio  
// FROM citas 
// LEFT OUTER JOIN usuarios 
// ON citas.usuarioId=usuarios.id 
// LEFT OUTER JOIN citasservicios 
// ON citasservicios.citaId = citas.id
// LEFT OUTER JOIN servicios
// ON citasservicios.servicioId = servicios.id;