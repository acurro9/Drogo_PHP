<?php
    //Se realiza la conexión a la base de datos
    function conectarDB(){
        $db= mysqli_connect('localhost', 'root', '', 'drogoDB');
        if (!$db){
            echo "Error: no se pudo conectar a la base de datos";
            exit;
        }
        return $db;
    }