<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        $auth = new Usuario();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //Comprobar que exsite el usuario
                $usuario = Usuario::WHERE('email', $auth->email);
                if ($usuario) {
                    //Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //Autenticar el usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        //Redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin;
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
        $router->render('auth/login', ['alertas' => $alertas, 'auth' => $auth]);
    }

    public static function logout(Router $router)
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router)
    {

        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $auth->validarEmail();
            if (empty($alertas)) {
                $usuario = Usuario::WHERE('email', $auth->email);
                if ($usuario && $usuario->confirmado === "1") {
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no exsite o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', ['alertas' => $alertas]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);
        //Buscar usuario por su token
        $usuario = Usuario::WHERE('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Lee la nueva contraseña y la guarda
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();
            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', ['alertas' => $alertas, 'error' => $error]);
    }

    public static function crear(Router $router)
    {
        //Alertas vacias
        $alertas = [];
        $usuario = new Usuario($_POST);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar();
            $alertas =  $usuario->validarNuevaCuenta();
        }

        //Revisar que alertas este vacio
        if (empty($alertas)) {
            $resultado = $usuario->existeUsuario();

            if ($resultado->num_rows) {
                $alertas = Usuario::getAlertas();
            } else {
                //Hashear password
                $usuario->hashPassword();

                //Generar token unico
                $usuario->crearToken();
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarConfirmacion();

                //Crear usuario
                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('Location: /mensaje');
                }
            }
        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::WHERE('token', $_GET['token']);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', ['alertas' => $alertas]);
    }
}
