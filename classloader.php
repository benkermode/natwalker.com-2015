<?

class smartClassMapper  {
  private static $instance = __CLASS__;

  protected function __construct () { 
  }

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }

  public static function autoloadClass ( $class ) {
    $file = self::classExists($class);
    if ( $file ) {
      require_once( $file );
    } else {
            die();
    }
  }
  private function classExists ( $class ) {
    $file = T_CLASS_DIR . '/' . $class . '.php';
    return ( file_exists ( $file ) ) ? $file : false;
  }
}

?>
