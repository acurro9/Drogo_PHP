<?php
//Para la tabla de los lockers

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

    //Se inicializan las variables a vacio
    $id="";

    //Se realiza la consulta para tener el nº total de lockers
    $consultaCont="SELECT count(*) as contador FROM locker;";
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
        $cant="No hay lockers.";
    } else{
        //Se coge el total de páginas redondeando hacia arriba
        $totalPaginas=ceil($cantPropiedades/$ppp);
        //Se calculan el limit y el offset
        $offset=($pagina-1)*$ppp;
        $limit= $ppp;
        $cant="";
    }
    //Se crea la query para mostrar los datos y se envia al servidor
    $query = "SELECT refLocker, direccion, passwordLocker from locker limit $limit offset $offset;";
    $datos = mysqli_query($db, $query);

    // En caso de realizar el post para la eliminación
    if ($_SERVER['REQUEST_METHOD']==='POST'){
        //Se guarda la referencia y se realiza la consulta para la eliminación, en caso de ser correcto aparece un mensaje exito
        $id=$_POST['refLocker'];
        $consulta="DELETE from locker where refLocker='$id';";
        $result=mysqli_query($db, $consulta);
        if($result){
            header("Location: /Lockers/lockers.php?res=3");
        }
    }
    
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/lockers.css">
<main>
    <h1 class="abajo">Lockers: </h1>
    <div class="centrado">
        <!--Se le pide al usuario el número de pedidos por página-->
        <form class="paginado">
            <fieldset>
                <legend style="color: white;">Lockers por página: </legend>
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
    <!-- Si no hay lockers se imprime -->
<?php echo '<p class="error">'.$cant.'</p>'?>
    <table>
        <thead>
            <tr>
                <th>Referencia del Locker</th>
                <th>Direccion del Locker</th>
                <th>Contraseña del Locker</th>
                <th>Operaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila=mysqli_fetch_assoc($datos)){ ?>
                <tr>
                    <td><?php echo $fila['refLocker'] ?></td>
                    <td><?php echo $fila['direccion'] ?></td>
                    <td><?php echo $fila['passwordLocker'] ?></td>
                    <td>
                        <div class="bloque">
                            <!-- Formulario para eliminar el locker seleccionado -->
                            <form action="<?php $_SERVER[ 'PHP_SELF' ]; ?>" method="post" onsubmit="return confirmEliminado()" class="formEliminado">
                                <input class="bTabla" type="submit" value=" ">
                                <input class="oculto" type="hidden" name="refLocker" value=<?php echo $fila['refLocker'];?>>
                            </form>
                            <a href="/Lockers/actualizarLockers.php?locker=<?php echo $fila['refLocker'];?>" class="bTabla act"><img src="/assets/icons/writer.svg" alt=""></a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
        <!--Para moverse entre las páginas-->
        <div class="pagComplete">
            <?php if($cantPropiedades!=0){ if($pagina>1){?>
                
            <a class="pag" href="/Lockers/lockers.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina-1?>"><</a><?php } ?>
            <?php for($i=0; $i<$totalPaginas; $i++){ ?>
                <a class="pag" href="/Lockers/lockers.php?producto=<?php echo $ppp;?>&pagina=<?php echo $i+1;?>" <?php echo $pagina==$i+1?'style="color: #71B100; font-weight: 800;"':'';?>><?php echo $i+1;?></a>
                <?php } 
                if($pagina<$totalPaginas){
                    ?>
            <a class="pag" href="/Lockers/lockers.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina+1?>">></a>
                    <?php } } ?>
        </div>
    <div class="centro">
        <a href="/Lockers/crearLockers.php" class="buton grande">Crear Locker</a>
    </div>
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/index.js"></script>
<?php
//Se incluye el footer
    incluirTemplate('footer');
?>