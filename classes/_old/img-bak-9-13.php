<?

class img  {
  private static $instance = __CLASS__;
  public $image;

  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->sections;
  }

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }
  
  public function getImage ( $id, $w='', $h='', $attribute='' )
  {
    ini_set("memory_limit","10000M");

    if ( !( $id > -1 ) )
      {
        return '';
      }
    if ( !$attribute )
      {
        $attribute = '';
      }
    $file = IMG_UPLOAD_DIR . '/' . $id . '.jpg';

    $info = getimagesize ( $file );
    //       genFuncs::getInstance()->spit ( $info );

    //w and h are max values: new w OR new h should match them: so try
    //one first: h
    $target_h = $h;

    $width_height_ratio = $info [ 0 ] / $info [ 1 ];
    $target_w = round ( $width_height_ratio * $target_h, 0 );

    if ( ( $target_w > $w ) && ( $w != false ) )
      {
        $target_w = $w;
        $target_h = round ( $target_w / $width_height_ratio, 0 );
      }
    $h = $target_h;
    $w = $target_w;

    //if orig image height <= desired height, then return original image
    if ( $info[ 1 ] <= $h )
      {
        return '<img src="' . REL_UPLOAD_DIR . '/' . $id . '.jpg" width="' . $info [ 0 ] . '" height="' . $info [ 1 ] . '"' . $attribute . '/>';
      }
    else //resize required
      {
        /*----this system assumes constant height----*/
        /*----create system that can request SET HEIGHT OR LONGEST EDGE----*/
        //look in the folder named height of image
        $target_dir = ABS_RESIZED_DIR . '/' . $h;
        $existing_file = $target_dir  . '/' . $id . '.jpg';

        $rel_existing_file = REL_RESIZED_DIR . '/' . $h . '/' .  $id . '.jpg';
        if ( file_exists ( $existing_file ) )
          {
            genFuncs::getInstance()->spit ( 'file exists' );
            $existing_info = getimagesize ( $existing_file );
            return '<img src="' . $rel_existing_file .  '" width="' . $existing_info[0] . '" height="' . $existing_info[1] . '"' . $attribute . '/>';
          }
        else 
          {
            if ( !is_dir ( $target_dir ) )
              {
                mkdir ( $target_dir );
              }
            //load
            $this->image = imagecreatefromjpeg( $file );
            //resize

            $new_image = imagecreatetruecolor($w, $h );
            //imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $w, $h, imagesx ( $this->image ), imagesy ( $this->image );
            imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $w, $h, $info [ 0 ], $info [1] );
            $this->image = $new_image;

            imagejpeg( $this->image, $existing_file , IMG_COMPRESSION );
            return '<img src="' . $rel_existing_file .  '" width="' . $w . '" height="' . $h . '"' . $attribute . '/>';

            imagedestroy ( $this->image );
            imagedestroy ( $new_image );
          }
      }

  }

}

?>
