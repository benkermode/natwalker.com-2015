<?

class genFuncs  {
   private static $instance = __CLASS__;
  public $sections = array();
  public $image;
  public $s;

  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->sections;
  }

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }
  
  public function tpInc ( $tp )
  {
    if ( file_exists ( $tp ) )
      {
        include $tp;
      }
  }

  public function getSectionsString()
  {
    $string = $this->s['section'];
    $string .= ( $this->s['subsect'] ) ?    '_' . $this->s['subsect'] : '';
    return $string;
  }

  public function spit ( $what ) 
  {
    echo '<div class="spit">';
    if ( is_array ( $what ) )
      {
        echo ( 'array:<br>' );
        foreach ( $what as $k => $v ) 
          {
            if ( is_array ( $v ) ) 
              {
                echo ( '&nbsp;&nbsp;array<br>');
                foreach ( $v as $k2=>$v2 ) {
                  echo '&nbsp;&nbsp;';
                  echo ( $k2 . '=' . $v2 . '<br>');
                }
              } else {
              echo ( $k . '=' . $v . '<br>');
            }
          }
      } 
    else
      {
        echo $what;
      }
    echo '<br/>';
    echo '</div>';
  }

  public function makeurl ( $raw, $true )
  {
    if ( $true == true )
      {
        $return = strtolower ( str_replace ( ' ', '-', $raw ) );
      }
    else 
      {
        $return = ucwords ( str_replace ( '-', ' ', $raw ) );
      }
    return $return;
  }
}

?>
