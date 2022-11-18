<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {


    // Iniciar Sesión
    public static function login(Router $router) {

        $alertas = [];

        if( $_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if( empty( $alertas ) ) {

                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                // Si existe email
                if( $usuario ) {
                    // Verificar el password
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ) {

                        // Autenticar al usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento

                        if( $usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
        ]);
    }



    // Cerrar Sesión
    public static function logout() {

        session_start();

        $_SESSION = [];

        header('Location: /');
    }



    // Olvidé mi password
    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if( empty( $alertas ) ) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {

                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Mensaje de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');

                } else {
                    // Mensaje de Error
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
        
    }



    // Recuperar mi password
    public static function recuperar(Router $router) {
        
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if( empty( $usuario ) ) {
            Usuario::setAlerta('error', 'Token expirado o no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if( empty( $alertas ) ) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if( $resultado ) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);

    }



    // Crear cuenta
    public static function crear(Router $router) {
        
        $usuario = new Usuario;

        //Alertas vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Sincronizar con los datos registrados en POST
            $usuario->sincronizar($_POST);

            // Validar y recojer las alertas
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas este vacío
            if( empty( $alertas ) ) {

                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if( $resultado->num_rows) {
                    // Esta registrado
                    $alertas = Usuario::getAlertas();
                } else {
                    // No esta registrado
                    // Hashear el Password
                    $usuario->hashPassword();

                    // Generar un token único
                    $usuario->crearToken();

                    // // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if( $resultado ) {
                        header('Location: /mensaje');
                    }

                }
            }
            

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }



    public static function mensaje(Router $router) {

        $router->render('auth/mensaje');
    }



    public static function confirmar(Router $router) {

        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if ( empty($usuario) || $usuario->token === '' || $usuario->token === null ) {
            
            // Almacenar mensaje de error
            Usuario::setAlerta('error', 'Token expirado o no válido');

        } else {

            // Modificar campos
            $usuario->confirmado = '1';
            $usuario->token = null;

            // Guardar cambios
            $usuario->guardar();

            // Almacenar mensaje de exito
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        // Obtener las alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

}