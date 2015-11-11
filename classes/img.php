<?

class img  {
  private static $instance = __CLASS__;
  public $rawSourceImage;
  public $imageData = array();
  private $fileExisted;
  private $imageId;
  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->sections;
  }

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }
  
  public function checkImagick()
  {
    if(extension_loaded('gd')) {
      print_r(gd_info());
    }
    else {
      echo 'GD is not available.';
    }

    if(extension_loaded('imagick')) {
      $imagick = new Imagick();
      print_r($imagick->queryFormats());
    }
    else {
      echo 'ImageMagick is not available.';
    }

  }

  //new function to batch all sizes for one image at once
  //less taxing functions like  $file = IMG_UPLOAD_DIR . '/' . $id . '.jpg' and $info = getimagesize ( $file );
  public function batchOneImageToAllSizes ( $id, $resize_dimensions )
  {
    $this->imageId = $id;
    ini_set("memory_limit","10000M");
    $this->fileExisted = 0;

    if ( !( $id > -1 ) )
      {
        return '';
      }
    $file = IMG_UPLOAD_DIR . '/' . $id . '.jpg';
    //only do this once for one original image, then resize multiple times
    $info = getimagesize ( $file );
    $width_height_ratio = $info [ 0 ] / $info [ 1 ];

    //this will be set to true if ANY of the sizes are created
    $this->imageData [ 'did_resize' ] = false;

    //create the array that will be converted to a string in the main image table
    //add a size for every iteration to the array, but only save it to the db if a new image is created
    $this->imageData [ 'has_sizes' ] = array();

    foreach ( $resize_dimensions as $k => $v ) 
      {
        $target_w = $v [0];
        $target_h = $v [1];

        if ( $target_w == -1 ) 
          {
            $fixed_dimension = 'h';
            $target_w = round ( $width_height_ratio * $target_h, 0 );
          } 
        else if ( $target_h == -1 ) 
          {
            $fixed_dimension = 'w';
            $target_h = round ( $target_w / $width_height_ratio, 0 );
          }

        //echo ( '<br>target_w: ' . $target_w . ', target_h: ' . $target_h ); 

        $h = $target_h;
        $w = $target_w;

        $this->imageData [ 'has_sizes' ] [] = array ( $w, $h, $fixed_dimension );

        //if any required dimenions are larger than the original image, do nothing
        if ( ( $info[ 0 ] <= $w ) || ( $info[ 1 ] <= $h ) )
          {
          }
        else //resize required
          {
            //target directory will be named according to the required/standardizing dimension, eg 200w or 720h
            if ( $fixed_dimension == 'w' )
              {
                //eg resized/200w/
                $target_dir = ABS_RESIZED_DIR . '/' . $w . 'w';
                $rel_existing_file = REL_RESIZED_DIR . '/' . $w . 'w/' .  $id . '.jpg';
              }
            else
              {
                //eg resized/720h/
                $target_dir = ABS_RESIZED_DIR . '/' . $h . 'h';
                $rel_existing_file = REL_RESIZED_DIR . '/' . $h . 'h/' .  $id . '.jpg';
              } 
            $existing_file = $target_dir  . '/' . $id . '.jpg';

            if ( file_exists ( $existing_file ) )
              {
                $this->fileExisted = 1;
              }
            else 
              {
                //we only set this to true in the loop, never to false
                //set to false outside the loop so the var is TRUE if AT LEAST ONE resizing occurred
                $this->imageData [ 'did_resize' ] = true;

                if ( !is_dir ( $target_dir ) )
                  {
                    mkdir ( $target_dir );
                  }
                //imagecreatefromjpeg() returns an image identifier representing the image obtained from the given filename
                $this->rawSourceImage = imagecreatefromjpeg( $file );

                //imagecreatetruecolor() returns an image identifier representing a black image of the specified size.                //we create a black image of our desired resize dimenions
                $new_image = imagecreatetruecolor( $w, $h );

                //imagecopyresampled ( $dst_image , $src_image , $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h )
                //imagecopyresampled() copies a rectangular portion of one image to another image
                //new_image (dst_image) is our blank new image at resized size
                //this->rawSourceImage (src_image) is the original file
                //the co-ords just make it an entire copy
                imagecopyresampled( $new_image, $this->rawSourceImage, 0, 0, 0, 0, $w, $h, $info [ 0 ], $info [1] );

                //if $fixed_dimension is the width, then this is a thumbnail 
                $jpg_compression = ( $fixed_dimension == 'w' ) ? THUMB_COMPRESSION : HERO_COMPRESSION;

                //create the new file
                imagejpeg( $new_image, $existing_file , $jpg_compression );

                //now that a copy has been saved, destroy the new image
                imagedestroy ( $new_image );

                //destroy original image
                imagedestroy ( $this->rawSourceImage );

              }
          }

        //update the db for every image we check, whether they need resize or not. ensures latest sizes are in db
           $this->updateDbWithAllSizesForOneImage ();    

      }//end for each of $resize_dimensions

    //only update the database with sizes if a new image was created
    //always assume that the db was updated correctly after every image creation 


    //if ( $this->imageData [ 'did_resize' ] == true ) 
    //{
    //        $this->updateDbWithAllSizesForOneImage ();    
        //}

    return $this->imageData;

  }//end function batchOneImageToAllSizes

  public function updateDbWithAllSizesForOneImage ()
  {
    $has_sizes_string = '';
    foreach ( $this->imageData [ 'has_sizes' ] as $k=>$v ) 
      {
        $has_sizes_string .= $v [0] . ':' . $v [1] . ':' . $v [2] . '.';
      }
    $has_sizes_string = substr ( $has_sizes_string, 0, strlen ( $has_sizes_string ) - 1 );

    $q = 'update images set image_has_sizes = "' . $has_sizes_string . '" where image_id="' . $this->imageId . '"';
    $result = tdb::getInstance()->queryNoResult( $q );

  }//end  func updateDBWithAllSizesForOneImage

}

?>
