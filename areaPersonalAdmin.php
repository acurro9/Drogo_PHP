<?php 
//Para el area personal del admin

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require './includes/funciones.php';
    incluirTemplate('header');
    require './includes/config/database.php';
    $db=conectarDB();
    
    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="./css/areaPersonal.css">

<div class="intro">
<h1 class="bvnd">¡Bienvenido/a!</h1>
    <h2 class="userOpt_title">Elige una opción: </h2>
</div>
<section class="banner_bottom">
    <div class="icon_drogo_div_area">
        <img src="./assets/logoimg/paquete.png" alt="icono drogo" class="icon_area">
    </div>
</section>

<main class=main_personalArea>
    
    <section class="userOptions">
        <div class="userOpt leftPannel">
            <div class="user_inner_left">
                <div class="userOpt account">
                    <img src="./assets/icons/lock.svg" alt="" class="lock_icon">
                    <a href="#" class="a_title">Usuarios</a>
                </div>
                <div class="userOpt pedido">
                    <img src="./assets/icons/lock.svg" alt="" class="lock_iconP">
                    <a href="#"  class="a_title">Pedidos</a>
                </div>
                <div class="userOpt locker">
                    <img src="./assets/icons/lock.svg" alt="" class="lock_iconL">
                    <a href="#"  class="a_title">Lockers</a>
                </div>
            </div>
        </div>
    </section>

    <section class="miCuenta hidden">
                <div class="userOptRight rightPannel">
                    <div class="user_inner_right">
                        <div class="userOptRight mostrar">
                            <img src="./assets/icons/detective.svg" alt="detective">
                            <a href="/Usuarios/usuario.php" class="miCuentaOpcion a_title">Ver cuentas</a>
                        </div>
                        <div class="userOptRight miCuentaOpciones">
                            <img src="./assets/icons/trash.svg" alt="basura">
                            <a href="/bloquearUsuario.php" class="miCuentaOpcion a_title">Bloquear cuentas</a>
                        </div>
                    </div>
                </div>
    </section>  
    <section class="miPedido hidden">
                <div class="userOptRight rightPannel">
                    <div class="user_inner_right">
                    <div class="userOptRight mostrar">
                            <img src="./assets/icons/detective.svg" alt="detective">
                            <a href="/Pedidos/pedidos.php" class="miCuentaOpcion p_title">Ver Pedidos</a>
                        </div>
                        <div class="userOptRight miCuentaOpciones">
                            <img src="./assets/icons/writer.svg" alt="punta de boli">
                            <a href="Pedidos/crearPedido.php" class="miCuentaOpcion" class="a_title">Crear Pedidos</a>
                        </div>
                        <div class="userOptRight miCuentaOpciones">
                            <img src="./assets/icons/send.svg" alt="punta de boli">
                            <a href="Pedidos/entregas.php" class="miCuentaOpcion" class="a_title">Ver distribuciones</a>
                        </div>
                    </div>
                </div>
    </section> 
    <section class="miLocker hidden">
                <div class="userOptRight rightPannel">
                    <div class="user_inner_right">
                    <div class="userOptRight mostrar">
                            <img src="./assets/icons/detective.svg" alt="detective">
                            <a href="/Lockers/lockers.php" class="miCuentaOpcion p_title">Ver Lockers</a>
                        </div>
                        <div class="userOptRight miCuentaOpciones">
                            <img src="./assets/icons/writer.svg" alt="punta de boli">
                            <a href="/Lockers/crearLockers.php" class="miCuentaOpcion" class="a_title">Crear Lockers</a>
                        </div>
                    </div>
                </div>
    </section> 
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/cuentaAdmin.js"></script>
<?php
//Se incluye el footer
    incluirTemplate("footer");
?>