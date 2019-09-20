<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':

    if( !empty( $_GET['id'] ) && isset( $_GET['id'] ) ) {

        $params = $_GET;
        $query  = "SELECT T.*,
        CONCAT( U.USR_NOMBRE, ' ', U.USR_AP_PATERNO ) AS 'POR'
        FROM ".$params['table']." T 
        LEFT OUTER JOIN users U ON T.CREADO_POR = U.USR_ID
        WHERE ID = ? AND OWNER = ?";
        
        $types  = "ii";
        $data = array( $params['id'], $_SESSION['ID'] );
        $result = $engine->executeQuery( $query, $data, $types, 1);   

    } else {
        $params = $_GET;
        $query  = "SELECT 
        T.CREADO_FECHA AS 'FECHA',
        T.ID AS 'ID',
        CONCAT( U.USR_NOMBRE, ' ', U.USR_AP_PATERNO ) AS 'POR',
        CONCAT( UU.USR_NOMBRE, ' ', UU.USR_AP_PATERNO ) AS 'MOD_POR'
        FROM ".$params['table']." T 
        INNER JOIN users U ON T.CREADO_POR = U.USR_ID
        LEFT OUTER JOIN users UU ON T.MODIF_POR = UU.USR_ID
        WHERE ESTADO = 1 AND OWNER = ? ORDER BY CREADO_FECHA";
        
        $types  = "i";
        $data = array( $_SESSION['ID'] );
        $result = $engine->executeQuery( $query, $data, $types, 1);
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