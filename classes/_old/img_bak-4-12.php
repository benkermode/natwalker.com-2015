<?

class img  {
  private static $instance = __CLASS__;
  public $image;

  public function __construct ( $sections ) {
    $this->sections = $sections;
  }

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }
  
  public function getImage ( $id, $w, $h )
  {
    $file = IMG_UPLOAD_DIR . '/' . $id . '.jpg';
    $info = getimagesize ( $file );
    //genFuncs::getInstance()->spit ( $info );

    if ( $h )
      {
        //h
        if ( $info[ 1 ] <= $h )
          {
            return '<img src="' . REL_UPLOAD_DIR . '/' . $id . '.jpg"/>';
          }
        else //resize required
          {
            //look in the folder named height of image
            $target_dir = ABS_RESIZED_DIR . '/' . $h;
            $existing_file = $target_dir  . '/' . $id . '.jpg';
            $rel_existing_file = REL_RESIZED_DIR . '/' . $h . '/' .  $id . '.jpg';
            if ( file_exists ( $existing_file ) )
              {
                $existing_info = getimagesize ( $existing_file );
                return '<img src="' . $rel_existing_file .  '" width="' . $existing_info[0] . '" height="' . $existing_info[1] . '"/>';
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
                $width_height_ratio = $info [ 0 ] / $info [ 1 ];
                $new_width = round ( $width_height_ratio * $h, 0 );

                $new_height = $h;
                if ( $new_width > $w )
                  {
                    $new_width = $w;
                    $new_height = round ( $width_height_ratio / $new_width, 0 );
                  }
                genFuncs::getInstance()->spit ( $new_width . ', ' . $new_height );

                $new_image = imagecreatetruecolor($new_width, $new_height );
                //imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, imagesx ( $this->image ), imagesy ( $this->image );
                imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $info [ 0 ], $info [1] );
                $this->image = $new_image;
                imagejpeg( $this->image, $existing_file , IMG_COMPRESSION );
                return '<img src="' . $rel_existing_file .  '" width="' . $new_width . '" height="' . $new_height . '"/>';
              }
          }
      }
    else 
      {
        return '<img src="' . REL_UPLOAD_DIR . '/' . $id . '.jpg"/>';
      }
  }


}

?>
