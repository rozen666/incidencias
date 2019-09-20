<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;

    if( empty($params['id']) ){
        $query  = "SELECT CONCAT(USR_NOMBRE, ' ', USR_AP_PATERNO) AS 'NOMBRE', USR_ID AS 'ID' FROM users";
        $result = $engine->executeQuery( $query );
    }else {
      $id_rol = (int) $params['id'] - 1;
      $id_rol = $id_rol === 0 ? 1 : $id_rol;
       $data = array($id_rol);
        $query  = "SELECT CONCAT(USR_NOMBRE, ' ', USR_AP_PATERNO) AS 'NOMBRE', USR_ID AS 'ID' FROM users WHERE USR_ROL = ?";
        $types  = "i";
        $result = $engine->executeQuery( $query, $data, $types );   
    }
   
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'post':
    $params = $_POST;
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'delete':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "";
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