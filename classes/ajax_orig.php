<?php

function module_try_buy ( &$data ) {

  list( $data['page'] ) = explode ( '/', $data['__key'] );

  $data['fields'] = form_fields();

  if ( $data['view'] == 'process' )
    {

      $valid = validate_form ( $data['user'] );

      if ( $valid )
        {


          $sql = "INSERT INTO `try_buy_user` ";
          $sql .= "(";

          foreach ( $data['fields'] as $k => $v ) 
            {
              $sql .= $k . ",";
            }
          // Lop off the last ','
          $sql = substr( $sql, 0, -1 ); 
          $sql .= ") VALUES (";
          // (val1,val2,val3,)
          foreach ( $data['fields'] as $k => $v ) 
            {
              if ( $k == 'timestamp' )
                {
                  $sql .= time() . ",";
                  continue;
                } 
              elseif ( $k == 'status' )
                {
                  $sql .= STATUS_LIVE . ",";
                  continue;
                } 
              else 
                {
                  $sql .= "'" . addslashes ( $data['user'][$k] ) . "',";
                }
            }
          // Lop off the last ','
          $sql = substr( $sql, 0, -1 ); 
          $sql .= ");";
          $result = db_exec ( $sql );

          //          $db_result = go_db ( $data );
          // go_db ( $data );
        }

      $json_send = array () ;
      $json_send['user'] = $data['user'];
      header ( 'Content-Type: application/json' );
      echo json_encode ( $json_send, true );
      exit;

      //        }
    }
  // elseif ( $data['view'] == 'process' )
  //   {
  //     $json_send = array () ;
  //     $json_send['user'] = $data['user'];
  //     header ( 'Content-Type: application/json' );
  //     echo json_encode ( $json_send, true );
  //     exit;
  //   }

  if ($data['__key'] == 'terms')
    {
      $data['terms'] = file_get_contents ( SITE_ROOT . 'data/' . $data['__this'] . '/terms.html' );
    }

  core_set_template ( 'try_buy' );
  core_head_add ( 'jquery' );
  core_set_title( 'ODA Try Buy Promotion' );
}

function form_fields ( )
{
  $fields = array();
  $fields['name_first'] = array ('text','Given Name',true);
  $fields['name_last'] = array ('text','Last Name',true);
  $fields['email'] = array ('text','Email',true);
  $fields['phone'] = array ('text','Phone',true);
  $fields['organisation'] = array ('text','Company',true);
  return $fields;
}



function validate_form ( &$user ) 
{
  $json_send = array();
  foreach ( $user as $k=>$v ) 
    {
      //            if ( $data['fields'][$k][2] == true ) {

      if ( $k == 'email' )
        {
          if ( $v == '' || ! validate_email ( $v ) )
            $user['__error'][$k] = true;
        }
      else 
        {
          if ( $v == '' )
            {
              $user['__error'][$k] = true;
            }
        }
      //              }
    }

  return $user['__error']
    ? false
    : true;
}




?>
