<?php

class tdb {
  private static $instance = __CLASS__;
  public $ob;
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $db = DB_NAME;
  public $insert_id;

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }

  public function __construct () { 
    $this->connect();
  }
  public function __clone () { 
  }

  public function connect () {
    // echo "host info: " . $host . '<br>' . $user . '<br>' . $pass . '<br>' . $db;
    $this->ob = mysqli_connect( DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (! $this->ob ) {
      //      errorLog::getInstance()->errors[] = mysqli_connect_error();
      echo 'problemo';
    } //else { echo 'connected'; }
  }

  public function getHash ( $pass ) 
  {
    $pass = hash_hmac ( 'sha256', $pass, T_HASH_SALT, true );
    $pass = stripslashes ($this->ob->real_escape_string( $pass ) );
    return $pass;
  }

  public function queryNoResult ( $q ) {
    $result = $this->ob->query( $q  ) ;
  }

  public function query ( $q ) {
    $result = $this->ob->query( $q  ) ;
    //mysqli always returns an array
    return $result->fetch_assoc(); 
  }

  public function getRow ( $table, $key, $val ) {
    $q = 'select *  from ' . $table . ' where ' . $key . '="' . $val . '"';
    //     echo '<br>' . $q . '<br>';
    $result = $this->ob->query( $q  ) ;
    return $result->fetch_assoc();
  }

  public function getRowByArray ( $table, $key_value_pairs_array ) {
    $q = 'select *  from ' . $table . ' where ';
    foreach ( $key_value_pairs_array as $k => $v ) {
      $q .= $k . '="' . $v . '" AND ';
    }
    $q = substr ( $q, 0, strlen( $q ) - 4 );
    //echo '<br>' . $q . '<br>';
    $result = $this->ob->query( $q  ) ;
    return $result->fetch_assoc();
  }

  public function getOne ( $table, $key, $val, $which ) {
    $q = 'select ' . $which . ' from ' . $table . ' where ' . $key . '="' . $val . '"';
    //     echo '<br>' . $q . '<br>';
    $result = $this->ob->query( $q  ) ;
    return $result->fetch_assoc();

  }

  public function deleteRow ( $table, $key, $val ) {
    $q = 'select from ' . $table . ' where ' . $key . '="' . $val . '"';
    // echo '<br>' . $q . '<br>';
    return $this->ob->query( $q  ) ;
  }

  public function getEverything ( $table, $order_by='', $order='' ) {
    $q = 'select *  from ' . $table;
    if ( $order_by )
      {
        $q .= ' order by ' . $order_by . ' ' . $order;
      }

    $result = $this->ob->query( $q  ) ;
    $all = array();
    while ( $all[] = $result->fetch_assoc()) {
    } 
    return $all;
  }

  public function getRows ( $table, $key='', $val='' )
  {
    $q = 'select *  from ' . $table;
    if ( $key )
      {
        $q .= ' where ' . $key . '=' . $val;
      }
    $result = $this->ob->query( $q );
    //    echo ( $q );
    return $result->num_rows;
  }

  public function getAll ( $table, $key='', $val='', $order_by='', $order='', $lower_limit='', $limit_num='', $where_key='', $where_val='' ) {

    $q = 'select *  from ' . $table;
    if ( $key )
      {
        $q .= ' where ' . $key . '="' . $val . '"';
      }
    if ( $where_key  )
      {
        $q .= ' and ' . $where_key . '=' . $where_val;
      }
    if ( $order_by )
      {
        $q .= ' order by ' . $order_by . ' ' . $order;
      }
    if ( is_numeric ( $lower_limit )  )
      {
        $q .= ' limit ' . $lower_limit . ', ' . $limit_num;
      }

    $result = $this->ob->query( $q  ) ;
    //genFuncs::getInstance()->spit ( $q );
  
    $all = array();
    if ( $result ) 
      {
        while ( $all[] = $result->fetch_assoc()) {
        } 
        return $all;
      } 
    else
      {
        return false;
      }
  }

  public function getFromQ ($q)
  {
    $result = $this->ob->query( $q  ) ;
    //genFuncs::getInstance()->spit ( $q );
    $all = array();
    if ( $result ) 
      {
        while ( $all[] = $result->fetch_assoc()) {
        } 
        return $all;
      } 
    else
      {
        return false;
      }
  }
}

?>