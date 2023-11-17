<?php
    include 'app.php';
    //Incluye el template indicado
    function incluirTemplate($nombre, $inicio=false){
        include TEMPLATES_URL."\\${nombre}.php";
    }
?>