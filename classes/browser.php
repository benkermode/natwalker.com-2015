<?php

class browser {
  private static $instance = __CLASS__;

  public $ieVersion;

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }

  public function __construct() {
    $this->checkBrowser();
  }


  public function checkBrowser ()
  {
    preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);

    if ( count ( $matches ) > 1 )
      {
        //echo ( 'version: ' . $version );
        $version = $matches[ 1 ];
        $this->ieVersion = $version;// ( $version <= 7 ) ? 7 : ( $version == 8 ) ? 8 : ( $version == 9 ) ? 9 : false;
      }
  }

}

?>