<?php 
//Para la tabla de Usuarios

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    // Se guardan el tipo de usuario, la respuesta y se crea el array de errores
    $tipo=$_SESSION["tipo"];
    $errores=[];
    $res=$_GET['res']??NULL;
    
    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }



    //Se realiza la consulta para tener el nº total de usuarios
    $consultaCont="SELECT count(*) as contador FROM usuario;";
    $datosCont=mysqli_query($db,$consultaCont);
    $data=mysqli_fetch_assoc($datosCont);
    $cantPropiedades=$data['contador'];
    $cantPropiedades2=$data['contador'];

    /*Se crean las variables para la paginacion
    Por defecto los productos por pagina son 5 y la página es 1, si en la url está presente cambia*/
    $ppp = 5;
    if (isset($_GET["producto"])) {
        $ppp = $_GET["producto"];
    }
    $pagina = 1;
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }

    if($cantPropiedades==0){
        $totalPaginas=1;
        $offset=0;
        $limit=10;
        $cant="No hay usuarios.";
    } else{
        //Se coge el total de páginas redondeando hacia arriba
        $totalPaginas=ceil($cantPropiedades/$ppp);
        //Se calculan el limit y el offset
        $offset=($pagina-1)*$ppp;
        $limit= $ppp;
        $cant="";
    }
    // Se realiza la consulta necesaria
    $consulta="SELECT * from usuario limit $limit offset $offset;";
    $result=mysqli_query($db, $consulta);
    
?>
<link rel="stylesheet" href="/css/usuarios.css">
<link rel="stylesheet" href="/css/styles.css">
<main>
<h1 class="abajo">Usuarios</h1>
    <div class="centrado">
        <!--Se le pide al usuario el número de pedidos por página-->
        <form class="paginado">
            <fieldset>
                <legend style="color: white;">Usrs x pag: </legend>
                <div class="cen">
                    <select name="producto">
                        <option <?php echo $ppp==3?'selected':''; ?> value=3>3</option>
                        <option <?php echo $ppp==5?'selected':''; ?> value=5>5</option>
                        <option <?php echo $ppp==10?'selected':''; ?> value=10>10</option>
                        <option <?php echo $ppp==20?'selected':''; ?> value=20>20</option>
                    </select>
                    <input type="submit" value=" " class="">
                </div>
            </fieldset>
        </form>
    </div>
<main>
    
    <table class="tabla">
    <?php
    // Se imprimen los errores si los hay
        foreach($errores as $error){ 
    ?>
                    
        <div class="error">
            <?php echo $error;?>
        </div>
    <?php
        }
    ?>

<?php
        // Se imprime el mensaje dependiendo de la respuesta 
    if(intval($res)===1){?>
        <p class="success">Usuario bloqueado con exito.</p>
    <?php }else  if(intval($res)===2){?>
        <p class="success">Usuario desbloqueado con exito.</p>
    <?php }else  if(intval($res)===3){?>
        <p class="success">Usuario actualizado con exito.</p>
    <?php } ?>

    <!-- Si no hay usuarios se imprimen -->
    <?php echo '<p class="error">'.$cant.'</p>'?>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Bloqueado</th>
                <th>Actualizar</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila=mysqli_fetch_assoc($result)){ $hash_usuario=$fila['id'];?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo $fila['username']; ?></td>
                <td><?php echo $fila['email']; ?></td>
                <td><?php echo $fila['tipo']; ?></td>
                <td>
                    <div class="bloque">
                        <?php 
                        // Para saber si el usuario está bloqueado o no y manda en la url la información necesaria
                            $query="SELECT username from bloqueado where id like '$hash_usuario';";
                            $resultado=mysqli_query($db, $query);
                            if($resultado->num_rows){
                                $action=2;
                                echo "Sí";
                            } else{
                                $action=1;
                                echo "No";
                            }
                        ?>
                        <a href="/bloquearUsuario.php?id=<?php echo $hash_usuario;?>&action=<?php echo $action;?>" class="bTabla act ult">
                            <!-- <center> -->
                                <img src="/assets/icons/edit.svg" alt="">
                            <!-- </center> -->
                        </a>
                    </div>
                </td>
                <!-- En caso de que no sea administrador el usuario de la tabla nos permite editar sus datos -->
                <?php if($fila['tipo']!='Administrador'){ ?>
                    <td>
                        <center>
                            <a href="/Usuarios/actualizarUsuario.php?usuario=<?php echo $fila['id'];?>" class="bTabla act"><img src="/assets/icons/writer.svg" alt=""></a>
                        </center>
                    </td>
                <?php } else {?>
                    <td><img src="/assets/icons/ban.svg" alt=""></td>
                <?php }?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!--Para moverse entre las páginas-->
    <div class="pagComplete">
            <?php if($cantPropiedades!=0){ if($pagina>1){?>
                
            <a class="pag" href="/Usuarios/usuario.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina-1?>"><</a><?php } ?>
            <?php for($i=0; $i<$totalPaginas; $i++){ ?>
                <a class="pag" href="/Usuarios/usuario.php?producto=<?php echo $ppp;?>&pagina=<?php echo $i+1;?>" <?php echo $pagina==$i+1?'style="color: #71B100; font-weight: 800;"':'';?>><?php echo $i+1;?></a>
                <?php } 
                if($pagina<$totalPaginas){
                    ?>
            <a class="pag" href="/Usuarios.usuario.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina+1?>">></a>
                    <?php } } ?>
        </div>
        <div class="centro">
            <a href="/areaPersonalAdmin.php" class="buton grande">Volver</a>
        </div>
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/index.js"></script>
<?php
    //Se incluye el footer
    incluirTemplate('footer');
?>