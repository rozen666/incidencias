<!DOCTYPE html>
<html>
<head>
  <title>Inicio de sesión</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
  <link rel="stylesheet" href="./frontend/css/styles.css">
</head>
<body>
<div class="ajax-layer"><img src="./frontend/images/loader.gif"></div>
<div class="container">
  <div class="row justify-content-center align-items-center">
    <div class="col-md-6 mt-5 p-5 bg-light border border-dark rounded">
      <h3 class="text-center mb-3">Bienvenido</h3>
      <form>
      <div class="form-group">
        <input type="text" class="form-control dataCollector" 
        data-required= "1" data-regex="0" data-key="name" placeholder="Nombre de usuario"/>
      </div>

      <div class="form-group">
        <input type="password" class="form-control dataCollector" 
        data-required= "1" data-regex="0" data-key="password" placeholder="Contraseña"/>
      </div>

      <button  type="submit" class="btn btn-block btn-primary" id="boton_ingresar">Ingresar</button>
    </form>
  </div>
</div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.js"></script>
  <script src="./frontend/js/core.js"></script>
  <script>
    $( ()=> {
      let activeCoords,
          xCoords = {};

      if( localStorage.getItem('coords') ) {
        localStorage.removeItem('coords');
      }

      var getPosition = ( pos ) => {
        activeCoords = true;
        xCoords.latitude = pos.coords.latitude;
        xCoords.longitude = pos.coords.longitude;
      };

      var errorPosition = ( err ) => {
        activeCoords = false;
        console.log( err );
      }

      if( "geolocation" in navigator ) {
        navigator.geolocation.watchPosition( getPosition, errorPosition );
        
      } else {
        activeCoords = false;
      }

      $(document).on("click","#boton_ingresar",function( e ){
        e.preventDefault(); e.stopPropagation();
        var info= dataCollector(this); 
        if (info.errors===0){
          delete info.errors;
          info.geo = { activeCoords, xCoords };
          localStorage.setItem('coords', JSON.stringify( xCoords ));
          callAjax(info, "get", "core/login", function(res){
            if( !res.success ) {
              swal( res.title, res.message, 'warning' );
            } else{
              window.location.href = "./landing.php";
            }

          });
        }
      });
    })
  </script>
</body>
</html>
