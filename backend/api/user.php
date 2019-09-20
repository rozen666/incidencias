<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;
    $data =array($params["id"]);
    $query  = "SELECT USR_ID AS 'ID', USR_NOMBRE AS 'NOMBRE', USR_USERNAME AS 'USERNAME', USR_AP_PATERNO AS 'PATERNO',
              USR_AP_MATERNO AS 'MATERNO', USR_ROL AS 'ROL', USR_JEFE_DIRECTO AS 'JEFE',USR_CREADO_FECHA AS 'CREADO_FECHA', 
              USR_MODIF_POR AS 'MODIF_POR', USR_MODIF_FECHA AS 'MODIF_FECHA', USR_ESTADO AS 'ESTADO' FROM users WHERE USR_ID = ?";
    $types  = "i";
    $result = $engine->executeQuery( $query, $data, $types, 1);
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['meta'] = $result['meta'];
  break;

  case 'post':
    $params = $_POST;
    $n = $params['nombre'];
    $user = $params['username'];
    $paterno = $params['apellidopa'];
    $materno = $params['apellidoma'];
    $rol = $params['rol'];
    $jefe = $params['jefe'];
    $active = 1;
    $password = password_hash(sha1( md5( 'p3ps1temporal8u4RD5')), PASSWORD_DEFAULT);
    $data = array($n, $user, $paterno, $materno, $rol, $jefe, $_SESSION["ID"], $password, $active);
    $query  = "INSERT INTO users 
    (USR_NOMBRE, USR_USERNAME, USR_AP_PATERNO, 
    USR_AP_MATERNO, USR_ROL, USR_JEFE_DIRECTO, 
    USR_CREADO_FECHA, USR_CREADO_POR, USR_PASSWORD, USR_ESTADO) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $types  = "ssssiiisi";
    $result = $engine->executeQuery( $query, $data, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);
    $name = $params['nombre'];
    $paterno = $params['apellidopa'];
    $materno = $params['apellidoma'];
    $rol = $params['rol'];
    $jefe = $params['jefe'];
    $update =  $_SESSION['ID'];
    $iduser = $params['id'];
    $inactive = $params['active'];
    $data = array($name, $paterno, $materno,$rol, $jefe, $update, $inactive, $iduser);
    $query  = "UPDATE users SET 
    USR_NOMBRE = ?, USR_AP_PATERNO=?, 
    USR_AP_MATERNO=?, USR_ROL=?, USR_JEFE_DIRECTO=?,
    USR_MODIF_POR= ?, USR_ESTADO = ?, USR_MODIF_FECHA= NOW() WHERE USR_ID = ?";
    $types  = "sssiiiii";
    $result = $engine->executeQuery( $query, $data, $types, 1 );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['meta'] = $result['meta'];
  break;

  case 'delete':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "DELETE FROM users WHERE ";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  default:
    $response['success'] = false;
    $response['message'] = 'Método de petición no soportado';
  break;
}

header('Content-type: application/json');
echo json_encode( $response );
?>