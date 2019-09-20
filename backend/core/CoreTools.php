<?php
class CoreTools{

  public function __construct(){ }


  public function sessionLost( $inSession ){

    $return = false;
    if( empty( $inSession ) ){
      $return = true;

    }else{
      foreach( $inSession as $s ){
          if( empty( $s ) ){$return = true; break;}
      }
    }
    return $return;
  }

  public function hasAccess( $session, $required ) {
    $req = explode( ',', $required );
    if( empty( $session['ROL_ID'] ) || empty( $req ) ) {
      return false;
    } else {
      $rol = $session['ROL_ID'];
      if( !in_array( $rol, $req ) ) {
        return false;
      } else {
        return true;
      }
    }
  }

}














?>
