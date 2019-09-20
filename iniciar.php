<?php
session_start();
require './backend/core/CoreTools.php';
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Guard System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
  <link rel="stylesheet" href="./frontend/css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-md bg-light navbar-light border-bottom-dark">
  <a class="navbar-brand" href="#">
    <img src="frontend/images/pepsico.png" alt="logo" style="width:130px;">
  </a>

  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse justify-content-end navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="text-danger font-weight-bold nav-link" href="./backend/core/logout.php">Salir</a>
      </li>
    </ul>
  </div>  
</nav>

<div class="container-fluid sidebar-height">
  <div class="row h-100">
    <div class="col-md-2 bg-light pt-5 h-100 border-right-dark">

      <center>
      <img  src="frontend/images/perfil.png" 
      class="rounded-circle" alt="Imagen" width="90" height="80">
      <h3> <?php  echo $_SESSION['NOMBRE']; ?> <br>
      <small class="text-muted"><i> <?php echo $_SESSION['ROL']; ?> </i></small></h3>
      </center>
      <hr>
      <a  href="#" class="btn btn-block btn-outline-dark btn-sm mt-1">Inicidencias</a>
      <a  href="./users.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Usuarios</a>
      <a  href="./admin.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Administración</a>
    </div>
    <div class="col-md-10">
       <div class="row justify-content-center align-items-center">
    <div class="col-md-6 mt-5 p-5 bg-light border border-dark rounded">
      <h3 class="text-center mb-3">Inicio</h3>
      <form>
      <div class="form-group">
        <button type="button" class="btn btn-block btn-primary" id="boton_iniciar">Iniciar</button>
      </div>

      <div class="form-group">
        <button type="button" class="btn btn-block btn-danger ml-auto" id="boton_exit">Salir</button>
      </div>

    </form>
  </div>
    </div>

  </div>
</div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.js"></script>
  <script src="./frontend/js/core.js"></script>
  <script>
    $(()=>{

      $(document).on("click", "#firmarse", function(){
        callAjax(null, "POST", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.messagge, "warning");
          }
          else{
            location.reload();
          }
        })
      });

      $(document).on("click", "#desfirmarse", function(){
        const id= {id:$(this).data("id")};
        callAjax(id, "PUT", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.messagge, "warning");
          }
          else{
            location.href="backend/core/logout.php";
          }
        })
      });

    })
  </script>
</body>
</html>

<?php
}else{
  header('Location: /web');
}
?>