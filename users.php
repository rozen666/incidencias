<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$engine= new QueryEngine();
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
  if( $tools->hasAccess( $_SESSION, '1,2' ) ) {
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Guard System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
  <link rel="stylesheet" href="./frontend/css/styles.css">
  <style type='text/css'>
      input:disabled {
      background-color: transparent !important;
      border: 0 !important;
}
  </style>
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

    <?php
    $query = "SELECT COUNT(LOGA_ID) AS 'COUNT', LOGA_ID AS 'ID' FROM log_asistencia WHERE LOGA_USR = ".$_SESSION["ID"]." 
    AND LOGA_INICIO BETWEEN NOW() - INTERVAL 1 DAY AND NOW()";
    $resultado = $engine->executeQuery($query);
    $signed    = $resultado["data"][0]['COUNT'] > 0 ? true : false;
    if( $signed ){
      echo '<li class="nav-item pointer">
        <a class="text-info nav-link" data-id="'.$resultado["data"][0]["ID"].'" id="desfirmarse">Marcar salida laboral</a>
      </li>';
    }

    else {
      echo '<li class="nav-item pointer">
        <a class="text-success font-weight-bold nav-link" id="firmarse">Marcar inicio laboral</a>
      </li>';
    }
     ?>

      <li class="nav-item">
        <a class="text-danger font-weight-bold nav-link" href="./backend/core/logout.php">Salir</a>
      </li>
    </ul>
  </div>  
</nav>

<div class="container-fluid sidebar-height">
  <div class="row panel">
    <div class="col-md-2 bg-light pt-5 border-right-dark">
    <center>
      <img  src="frontend/images/perfil.png" 
      class="rounded-circle" alt="Imagen" width="90" height="80">
      <h3> <?php  echo $_SESSION['NOMBRE']; ?> <br>
      <small class="text-muted"><i> <?php echo $_SESSION['ROL']; ?> </i></small></h3><hr>
      </center>
      <?php 
      if( $signed ) { 

        $res   = $engine->executeQuery( "SELECT FORM_NOMBRE FROM forms WHERE FORM_ESTADO = 1 LIMIT 1" );
        $table = $res['data'][0]['FORM_NOMBRE'];

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2,3' ) ) ) {
          echo '<a  href="./reports.php" class="btn   btn-block btn-outline-dark btn-sm mt-1">Levantar incidencia</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./forms.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Formularios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./users.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Usuarios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./catalogs.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Catálogos</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./scaling.php" class="btn btn-block btn-outline-dark btn-sm mt-1">
          Escalamientos <span class="badge badge-primary scalings">0</span></a>';
        }
      }
      ?>
      </div>


    <div class="col-md-10 p-5">

      <!--Inicia tabla donde se mostraran a los usuarios que se tienen actualmente en la base de datos -->

        <div class="row">
          <div class="col-md-2 pt-5 bg-light p-3 border border-light rounded scrolluser">
            <h4 class="text-center text-primary">Usuarios</h4>
            
                  <?php 
                  $query = "SELECT USR_ID AS 'ID', 
                  CONCAT(U.USR_NOMBRE, ' ',U.USR_AP_PATERNO ) AS 'NAME', 
                  R.ROL_NOMBRE AS 'ROL' FROM users U 
                  INNER JOIN roles R ON R.ROL_ID = USR_ROL
                  WHERE USR_ID > 1
                  ORDER BY NAME";
                  $usuarios = $engine->executeQuery($query);
                    if (empty($usuarios['data'])) {
                      echo '<li >no existen registros</li>';
                    }
                    else{
                      foreach ($usuarios['data'] as $users) {
                      
                        echo '
                        <div class="pointer fs-8 text-center bg-dark text-light p-1 rounded border border-light name_user" data-id="'.$users["ID"].' ">
                        '.$users["NAME"].'<br>( <span class="fs-7 font-italic">'.$users['ROL'].'</span> )</div>
                        ';
                      }
                    }
                  ?>
          </div> 

          <!-- Inician los botones para ABC de usuarios -->

          <div class="col-md-10">
            <form id="formusers">
              <ul class="list-unstyled results">
                <li class="shadow p-3 mt-2">
                  <div class="row">
                    <div class="col-md-4">
                      <button  type="button" class="add btn btn-info btn-sm btn-block" id="new">
                        <i class="fas fa-plus font-weight-bold"></i>Nuevo usuario
                      </button>
                    </div>
                    <div class="col-md-4">
                      <input type="text" class="form-control form-control-sm dataCollector" 
                        data-required= "0" data-regex="0" data-key="id" id="id" placeholder="ID" hidden>
                      <button type="button" class="save btn btn-primary btn-sm btn-block" id="guardar_usuario">
                      <i class="fas fa-save font-weight-bold"></i> Guardar
                      </button>
                    </div>
                    <div class="col-md-4">
                      <button type="button" class="btn btn-info btn-sm btn-block" id="update">
                      <i class="fas fa-edit font-weight-bold"></i>Actualizar usuario
                      </button>
                    </div>
                  </div>
                </li>

            <!-- Inician los campos para el formulario de usuario -->

              <li class="shadow p-3 mt-2">
                <div class="row">
                  <div class="col-md-4">
                    <label for="forgroup">Nombre:</label>
                    <input type="text" class="form-control form-control-sm dataCollector"
                     data-required= "1" data-regex="0" data-key="nombre" id="nombre" placeholder="Nombre" autocomplete="off">    
                  </div>
                    <div class="col-md-4">
                      <label for="forgroup">Apellido Paterno:</label>
                      <input type="text" class="form-control form-control-sm dataCollector" data-required= "1" data-regex="0" data-key="apellidopa" id="apellidopa" placeholder="Apellido paterno" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                      <label for="forgroup">Apellido Materno:</label>
                      <input type="text" class="form-control form-control-sm dataCollector" data-required= "1" data-regex="0" data-key="apellidoma" id="apellidoma" placeholder="Apellido materno" autocomplete="off">

                    </div>
                </div>

                <div class="row mt-3">
                  <div class="col-md-4">
                    <label>Rol</label>
                    <select class="form-control form-control-sm dataCollector" data-required= "1" data-regex="0" data-key="rol" id="rol">

                      <!-- Inicia php para seleccionar los roles -->

                        <?php
                        $query = "SELECT ROL_ID AS 'ID', ROL_NOMBRE AS 'NOMBRE' FROM roles";
                        $roles = $engine->executeQuery($query);
                        if (empty($roles['data'])) {
                          echo '<option selected disabled>no existen registros</option>';

                        }
                        else{
                          echo '<option selected disabled>Selecciona un Rol</option>';
                          foreach ($roles['data'] as $rol) {
                            echo '<option value="'.$rol["ID"].'">'.$rol["NOMBRE"].'</option>';
                          }
                        }
                       ?>

                    </select>
                  </div>
                  <div class="col-md-4">
                    <label>Jefe directo</label>
                    <select class="form-control form-control-sm dataCollector" data-required= "1" data-regex="0" data-key="jefe" id="jefe">
                      <option selected disabled>Selecciona primero un rol</option>
                      <label class="fomr-check-label" for="active">
                        <input type="checkbox" class="form-check-input dataCollector" data-required= "0" data-regex="0" data-key="active" id="active" name="active" value="activo" hidden>Activo/Inactivo
                      </label>
                  </div>
                  <div class="col-md-4">
                    <label>Usuario</label>
                    <input type="text" class="form-control form-control-sm dataCollector" 
                    data-required= "1" data-regex="0" data-key="username" id="username" placeholder="Usuario" autocomplete="off">
                  </div>
                </div>
                </li>
              </ul>
            </form>
          </div>
        </div>     
    </div> 
  </div>
</div>

  <!-- Inician los scripts -->

</div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.js"></script>
  <script src="./frontend/js/core.js"></script>
  <script>
    $(()=>{
      var selector;

          // Script para firmarse

      $(document).on("click", "#firmarse", function(){
        callAjax(null, "POST", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.message, "warning");
          }
          else{
            location.reload();
          }
        })
      })

          // Scrip para desfirmarse

      $(document).on("click", "#desfirmarse", function(){
        const id= {id:$(this).data("id")};
        callAjax(id, "PUT", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.message, "warning");
          }
          else{
            location.href="backend/core/logout.php";
          }
        })
      })

          // Script para cambiar el jefe, dependiendo el rol seleccionado

      $(document).on("change", "#rol", function(){
        const id={id:$(this).val()};
        $("#jefe").empty();
        callAjax(id, "GET", "api/roles", function(rom){
          if(!rom.success){
            const option = `<option selected disabled>${rom.message}</option>`;
              $("#jefe").append(option);
          }
          else{
            if(rom.data.length === 0){
              const option = `<option selected disabled>${rom.message}</option>`;
              $("#jefe").append(option);
            }
            else{
              rom.data.forEach(function(room){
              const option = `<option value="${room.ID}">${room.NOMBRE}</option>`;
              $("#jefe").append(option);

            })
            }
          }
        },true,false);
      })

            // Script para obtener los datos de los usuarios ya registrados

      $(document).on("click", ".name_user", function(){
        // selector = this;
        $('#username').prop('readonly', true);
        $('#active').prop('hidden', false);
        const iduser = $(this).attr('data-id');
        callAjax({id:iduser}, "GET", "api/user", function(actuser){
          if(!actuser.success){
            swal(actuser.title, actuser.message, "warning");
          }
          else{
            
            let datos = actuser.data[0];
            estado = datos.ESTADO == 2?false:true;
            $('#nombre').val(datos.NOMBRE);
            $('#apellidopa').val(datos.PATERNO);
            $('#apellidoma').val(datos.MATERNO);
            $('#username').val(datos.USERNAME);
            $('#rol').val(datos.ROL);
            $('#rol').change();
            $('#jefe').val(datos.JEFE);
            $('#id').val(datos.ID);
            $('#active').prop('checked',estado);
            console.log(datos.ESTADO);
            // $('#fecha_regi').val(datos.CREADO_FECHA);
            // $('#fecha_modi').val(datos.MODIF_FECHA);
          }
        });
      });

            // Script para un nuevo formulario

      $(document).on("click", "#new", function(){
        // selector = this;
        $("#formusers")[0].reset();
        $("#jefe").empty();
        const option = `<option value="0">jefe</option>`;
        $("#jefe").append(option);
        $('#username').prop('readonly', false);
        $('#active').prop('hidden', true);
      });

            // Script para guardar un formulario o usuario nuevo

      $(document).on("click", "#guardar_usuario",function(){
        var datos= dataCollector(this); 
        datos.active = datos.active == 0?2:1;
        callAjax(datos, "POST", "api/user", function(guardar){
        if(!guardar.success){
          swal(guardar.title, guardar.message, "warning");
        }
        else{
          $("#formusers")[0].reset();
          $("#jefe").empty();
          const option = `<option value="0">jefe</option>`;
          $("#jefe").append(option);
          location.reload();
            
          }
        });
      });

        // Script para actualizar datos de usuario

      $(document).on("click", "#update",function(){
        var updatos= dataCollector(this);
        if (updatos.errors===0){
          updatos.active = updatos.active == 0?2:1;
        callAjax(updatos, "PUT", "api/user", function(actualizar){
          if(!actualizar.success){
            swal(actualizar.title, actualizar.message, "warning");
          }
          else{
          $(selector).click();
          location.reload();
          }
        });
      }
      });

      var count = getScaling( <?php echo $_SESSION['ID'] . ',"' . $table . '"'; ?> );
      $('.scalings').text( count );
      
    });

  </script>
</body>
</html>

<?php
  }else {
    header('Location: ./landing.php?error=Usuarios');  
  }
}else{
  header('Location: /web');
}
?>