<?php
header('Content-Type: application/json');
include 'QueryEngine.php';
$qb = new QueryEngine();

$response = array();
$pass = $_REQUEST['password'];
$user = $_REQUEST['name'];

/*if( $_REQUEST['geo']['activeCoords'] == 'false') {
  $response['success'] = false;
  $response['title']   = 'Error de aplicación';
  $response['message'] = 'La geolocalización está desactivada';
}*/

//else{
  $q = "SELECT
          U.USR_ID AS 'ID',
          CONCAT( U.USR_NOMBRE, ' ', U.USR_AP_PATERNO ) AS 'NOMBRE',
          R.ROL_NOMBRE AS 'ROL',
          R.ROL_ID AS 'ROL_ID',
          U.USR_PASSWORD AS 'PASSWORD',
          U.USR_USERNAME AS 'USERNAME',
          U.USR_JEFE_DIRECTO AS 'BOSS'
          FROM users U INNER JOIN roles R ON U.USR_ROL = R.ROL_ID
          WHERE U.USR_ESTADO = 1
          AND U.USR_USERNAME = ?";
  $p = array( $user ); 
  $t = 's';

  $r = $qb->executeQuery( $q, $p, $t, True );

  if( empty( $r['data'] ) ){
    $response['success'] = false;
    $response['title']   = 'Credenciales no autorizadas';
    $response['message'] = 'El usuario y/o contraseña son incorrectos';
    

  }else{

    $password = sha1( md5( 'p3ps1' . $pass . '8u4RD5' ) );
    $hash     = $r['data'][0]['PASSWORD'];

    /*if( !password_verify($password, $hash) ){
      $response['success'] = false;
      $response['title']   = 'Credenciales no autorizadas';
      $response['message'] = 'El usuario y/o contraseña son incorrectos';
      $response['pass']   = $password;
      $response['has']   = $hash;

    }else{*/
      session_start();
      session_regenerate_id( true );
      $_SESSION['ID']        = (int) $r['data'][0]['ID'];
      $_SESSION['USERNAME']  = $r['data'][0]['USERNAME'];
      $_SESSION['NOMBRE']    = $r['data'][0]['NOMBRE'];
      $_SESSION['ROL']       = $r['data'][0]['ROL'];
      $_SESSION['BOSS']       = $r['data'][0]['BOSS'];
      $_SESSION['ROL_ID']    = (int) $r['data'][0]['ROL_ID'];
      $response['success']   = true;
    }
  //}
//}

echo json_encode( $response );

?>
