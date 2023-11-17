<?php 
//Para la tabla de los usuarios suscritos al newsletters

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }
    $res=$_GET['res']??NULL;
    $tipo=$_SESSION['tipo'];

    //Se realiza la consulta para tener los correos del newsletter
    $consultaCont="SELECT count(*) as contador FROM newsletter;";
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
        $cant="No hay emails para el newsletter.";
    } else{
        //Se coge el total de páginas redondeando hacia arriba
        $totalPaginas=ceil($cantPropiedades/$ppp);
        //Se calculan el limit y el offset
        $offset=($pagina-1)*$ppp;
        $limit= $ppp;
        $cant="";
    }

    //Se realiza la query
    $query="SELECT * from newsletter limit $limit offset $offset;";
    $datos=mysqli_query($db, $query);

    //En caso de hacer el post de eliminar
    if ($_SERVER['REQUEST_METHOD']==='POST'){
        //Se guardan el id y se realiza la query
        $id=$_POST['id'];
        $consulta="DELETE from newsletter where email='$id';";
        $result=mysqli_query($db, $consulta);
        if($result){
            header("Location: /Admin/newsletter.php?res=1");
        }
    }
    
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/mensaje.css">
<link rel="stylesheet" href="/css/styles.css">

<main>
<h1 class="abajo">Newsletter</h1>
<div class="centrado">
        <!--Se le pide al usuario el número de pedidos por página-->
        <form class="paginado">
            <fieldset>
                <legend style="color: white;">Emails por página: </legend>
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
    <!-- Tabla del newsletter -->
    <table class="tabla dos">
    <?php
    // En caso del resultado ser 1
        if(intval($res)===1){?>
            <p class="success">Usuario borrado con exito.</p>
        <?php } ?>
        <?php echo '<p class="error">'.$cant.'</p>'?>
        <thead>
            <tr>
                <th>Email</th>
                <th>Borrar</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila=mysqli_fetch_assoc($datos)){ ?>
            <tr>
                <td><?php echo $fila['email']; ?></td>
                <td class="centrar">
                    <!-- Formulario para borrar un email -->
                    <form action="<?php $_SERVER[ 'PHP_SELF' ]; ?>" method="post" onsubmit="return confirmEliminado()" class="formEliminado">
                        <input class="bTabla" type="submit" value=" ">
                        <input class="oculto" type="hidden" name="id" value=<?php echo $fila['email'];?>>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!--Para moverse entre las páginas-->
    <div class="pagComplete">
            <?php if($cantPropiedades!=0){ if($pagina>1){?>
                
            <a class="pag" href="/Admin/newsletter.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina-1?>"><</a><?php } ?>
            <?php for($i=0; $i<$totalPaginas; $i++){ ?>
                <a class="pag" href="/Admin/newsletter.php?producto=<?php echo $ppp;?>&pagina=<?php echo $i+1;?>" <?php echo $pagina==$i+1?'style="color: #71B100; font-weight: 800;"':'';?>><?php echo $i+1;?></a>
                <?php } 
                if($pagina<$totalPaginas){
                    ?>
            <a class="pag" href="/Admin/newsletter.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina+1?>">></a>
                    <?php } } ?>
        </div>
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/index.js"></script>
<?php
//Se incluye el footer
    incluirTemplate('footer');
?>