<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;
    if( !empty( $params['id'] ) && isset( $params['id'] ) ) {
      $query  = "SELECT FORM_NOMBRE AS 'NAME',
                 FORM_INPUTS AS 'INPUTS', FORM_LABELS AS 'LABELS',
                 FORM_PLACEHOLDER AS 'PLACE', FORM_REQUIRED AS 'REQ',
                 FORM_DISABLED AS 'DIS', FORM_ESTADO AS 'ESTADO' FROM forms WHERE FORM_ID = ?";
      $data = array( $params['id'] );
      $types  = "i";
      $result = $engine->executeQuery( $query, $data, $types );

    } elseif( !empty( $params['active'] ) && isset( $params['active'] ) ) {

      $query  = "SELECT FORM_NOMBRE AS 'NAME',
                 FORM_INPUTS AS 'INPUTS', FORM_LABELS AS 'LABELS',
                 FORM_PLACEHOLDER AS 'PLACE', FORM_REQUIRED AS 'REQ',
                 FORM_DISABLED AS 'DIS', FORM_ESTADO AS 'ESTADO' FROM forms WHERE FORM_ESTADO = 1";
      $result = $engine->executeQuery( $query );

    } else{
      $query  = "SELECT FORM_NOMBRE AS 'NAME',
                 FORM_INPUTS AS 'INPUTS', FORM_LABELS AS 'LABELS',
                 FORM_PLACEHOLDER AS 'PLACE', FORM_REQUIRED AS 'REQ',
                 FORM_DISABLED AS 'DIS', FORM_ESTADO AS 'ESTADO' FROM forms";
      $result = $engine->executeQuery( $query );
    }
    
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'post':
    $params = $_POST;
    if( $params['active'] == 1 ) {
      $engine->executeQuery( "UPDATE forms SET FORM_ESTADO = 2" );
    }

    $query  = "INSERT INTO forms (FORM_NOMBRE, FORM_INPUTS, FORM_LABELS, FORM_PLACEHOLDER, FORM_REQUIRED,
                FORM_DISABLED, FORM_CREADO_POR, FORM_CREADO_FECHA, FORM_ESTADO)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $types  = "ssssssii";
    $data = array( $params['name'], $params['inputs'], $params['labels'], 
                   $params['placeholders'], $params['requireds'], $params['disableds'], $_SESSION['ID'], $params['active']  );
    $result = $engine->executeQuery( $query, $data, $types );

    if( !empty( $result['data'] ) ) {
      $create_query = "CREATE TABLE IF NOT EXISTS `". $params['table'] ."`(
        `ID` int (8) NOT NULL AUTO_INCREMENT,
        `OWNER` int (8) NOT NULL,
        `CREADO_POR` int(8) NOT NULL,
        `CREADO_FECHA` datetime NOT NULL,
        `MODIF_POR` int(8) DEFAULT NULL,
        `MODIF_FECHA` datetime DEFAULT NULL,
        `FILES` varchar(500) DEFAULT NULL,
        `ESTADO` int(1) NOT NULL DEFAULT '1',";
      
      foreach( $params['labels_fields'] as $field ) {
        $create_query .= "`".strtoupper($field)."` varchar(500) DEFAULT NULL,
        ";
      }
  
      $create_query .= "PRIMARY KEY (`ID`),
      KEY `".$params['table']."_CREADO POR` (`CREADO_POR`),
      KEY `".$params['table']."_OWNER` (`OWNER`),
      KEY `".$params['table']."_MODIFICADO POR` (`MODIF_POR`),
      KEY `".$params['table']."_ESTADO` (`ESTADO`),
      CONSTRAINT `".$params['table']."_CREADO POR` FOREIGN KEY (`CREADO_POR`) REFERENCES `users` (`USR_ID`) ON UPDATE CASCADE,
      CONSTRAINT `".$params['table']."_ESTADO` FOREIGN KEY (`ESTADO`) REFERENCES `estados` (`EDO_ID`) ON UPDATE CASCADE,
      CONSTRAINT `".$params['table']."_MODIFICADO POR` FOREIGN KEY (`MODIF_POR`) REFERENCES `users` (`USR_ID`) ON UPDATE CASCADE,
      CONSTRAINT `".$params['table']."_OWNER` FOREIGN KEY (`OWNER`) REFERENCES `users` (`USR_ID`) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      
      $engine->executeQuery( $create_query );
      $response['title']   = '¡Ocurrió algo extraño!';
      $response['success'] = !empty( $result['data']) ;
      $response['message'] = $result['message'];
      $response['data']    = $result['data'];
    }

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);

    $engine->executeQuery( "UPDATE forms SET FORM_ESTADO = 2" );

    $query  = "UPDATE forms SET FORM_NOMBRE = ?, FORM_INPUTS = ?,
              FORM_LABELS = ?, FORM_PLACEHOLDER = ?, FORM_REQUIRED = ?,
              FORM_DISABLED = ?, FORM_MODIF_POR = ?, FORM_ESTADO = ?,
              FORM_MODIF_FECHA = NOW() WHERE FORM_ID = ?";
    $types  = "ssssssiii";
    $data = array( $params['name'], $params['inputs'], $params['labels'], $params['placeholders'], 
                   $params['requireds'], $params['disableds'], $_SESSION['ID'], $params['active'], $params['id']  );
    $result = $engine->executeQuery( $query, $data, $types, 1 );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['meta']    = $result['meta'];
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
