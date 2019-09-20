<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$tools  = new CoreTools();
$engine = new QueryEngine();
if( !$tools->sessionLost( $_SESSION ) ){
    if( $_SESSION['ROL_ID'] === 1 ){
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
  <a class="navbar-brand" href="landing.php">
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

    if($resultado["data"][0]['COUNT']>0){
      echo '<li class="nav-item pointer">
        <a class="text-info nav-link" data-id="'.$resultado["data"][0]["ID"].'" id="desfirmarse">Desfirmarse</a>
      </li>';
    }

    else {
      echo '<li class="nav-item pointer">
        <a class="text-info nav-link" id="firmarse">Firmarse</a>
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
        <a  href="./users.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Usuarios</a>
        <a  href="./admin.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Administración</a>
  	</div>
  	<div class="col-md-10 pl-5 pt-1">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#forms">Formularios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="./users.php">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#catalogs">Catálogos</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active p-3 mt-1 bg-light shadow" id="forms">
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        $query = "SELECT FORM_ID AS 'ID', FORM_NOMBRE AS 'NAME',
                        FORM_INPUTS AS 'INPUTS', FORM_LABELS AS 'LABELS',
                        FORM_PLACEHOLDER AS 'PLACE', FORM_REQUIRED AS 'REQ',
                        FORM_DISABLED AS 'DIS', FORM_REGEX AS 'REG', FORM_ACTIVE AS 'ACTIVE'
                        FROM forms";
                        $res = $engine->executeQuery( $query );
                        if( empty( $res['data'] ) ) {
                            echo '<h5 class="text-danger text-center">No hay formularios, crea uno</h5>';
                        }
                        ?>
                        <button class="btn btn-sm btn-info btn-block newForm">Nuevo formulario</button>
                        <div class="newFormConstructor">
                            <hr>
                            <form>
                            <select class="form-control form-control-sm dataCollector"
                            data-required="1" data-regex="0" data-key="field">
                                <option selected disabled>Selecciona un tipo de campo</option>
                                <option value="text">Texto general</option>
                                <option value="password">Contraseña</option>
                                <option value="textarea">Caja de texto</option>
                                <option value="radio">Radio</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                            <!--select class="form-control form-control-sm dataCollector mt-1"
                            data-required="0" data-regex="0" data-key="regex">
                                <option selected disabled>Reglas del campo</option>
                                <option value="number">Sólo números</option>
                                <option value="words">Sólo letras</option>
                                <option value="email">Correo electrónico</option>
                                <option value="password">Contraseña</option>
                            </select-->
                            <input type="text" class="form-control form-control-sm dataCollector mt-1" 
                            data-required="1" data-regex="0" data-key="label" placeholder="Etiqueta del campo">
                            <input type="text" class="form-control form-control-sm dataCollector mt-1" 
                            data-required="0" data-regex="0" data-key="value"placeholder="Valor por defecto">
                            <input type="text" class="form-control form-control-sm dataCollector mt-1"
                            data-required="0" data-regex="0" data-key="placeholder" placeholder="Texto de ayuda">
                            
                            <input type="checkbox" class="dataCollector"
                            data-required="0" data-regex="0" data-key="required"> <small>¿Es requerido?</small>
                            <input type="checkbox" class="dataCollector"
                            data-required="0" data-regex="0" data-key="disabled"> <small>¿Está deshabilitado?</small>
                            <button class="btn btn-success btn-sm btn-block addField">Agregar a vista previa</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8 preview">
                        <form>
                            
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane container fade p-3 mt-1 bg-light shadow" id="users">
                USERS
            </div>

            <div class="tab-pane container fade p-3 mt-1 bg-light shadow" id="catalogs">
                CATALOGS
            </div>
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
      
      $( () => {

        $(document).on('click', '.newForm', function(){
            $('.newFormConstructor').show('fast');
        });

        $(document).on('click', '.addField', function( e ){
            e.preventDefault(); e.stopPropagation();
            let data = dataCollector( this );
            console.log( data );
            if( data.errors === 0 ) {
                let input;
                const required = data.required === 1 ? 'required' : '',
                  disabled = data.disabled === 1 ? 'disabled' : '',
                  formControl = data.field === 'checkbox' || data.field === 'radio' ? '' : 'form-control form-control-sm';
                if( data.field === 'textarea' ) {

                    input = `
                    <div class="form-group">
                        <label class="font-weight-bold mt-1">${data.label}</label>
                        <textarea class="${formControl} dataCollector" 
                        placeholder="${data.placeholder}" ${required} ${disabled}
                        data-key="${data.label}" data-regex="${data.regex || 0}" data-required="${data.required}">${data.value}</textarea>
                    </div>`;

                } else{
                    input = `
                    <div class="form-group">
                        <label class="font-weight-bold mt-1">${data.label}</label>
                        <input type="${data.field}" class="${formControl} dataCollector" 
                        placeholder="${data.placeholder}" ${required} ${disabled} value="${data.value}"
                        data-key="${data.label}" data-regex="${data.regex || 0}" data-required="${data.required}">
                    </div>`;
                }
                const button = `<div class="w-50 mx-auto">
                                <button class="btn btn-info btn-sm btn-block test">Probar</button>
                            </div>`;
                $('.preview form').append( input );
                $('.preview form .mx-auto').remove();
                $('.preview form').append( button )
                $(this).parents('form')[0].reset();
            }
        });

        $(document).on('click', '.test', function(e){
            e.preventDefault(); e.stopPropagation();
            let data = dataCollector( this, 1 );
            console.log( data );
            if( data.errors === 0 ){
                const alert = `<div class="alert alert-success alert-dismissible mt-3">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>¡Correcto!</strong> Se envió correctamente la prueba
                                </div>`;
                $(this).after().after( alert );
            }
        });
      });
  
  </script>
</body>
</html>

<?php
    }else{
        echo '<h1 class="text-center text-danger">No tienes autorización de entrar en esta sección</h1>';
    }
}else{
  header('Location: /web');
}
?>