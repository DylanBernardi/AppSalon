<?php

namespace Model;

class AdminCita extends ActiveRecord
{
    protected static $tabla = 'CitasServicios';
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'servicio', 'precio'];
    public $id;
    public $cliente;
    public $hora;
    public $email;
    public $telefono;
    public $servicio;
    public $precio;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->cliente = $args['cliente'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->servicio = $args['servicio'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }
}
