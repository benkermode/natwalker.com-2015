<?php
class cron {
  private $s = array();
  private $gdAvailable;
  private $imagickAvailable;
  private $allImages;

  public function __construct () {
    $this->s = sectionsOb::getInstance()->section;
    $this->goCron();
  }

  public function goCron()
  {

    $this->allImages = tdb::getInstance()->getAll( 'images');
    array_pop ( $this->allImages );
    genFuncs::getInstance()->spit ( 'sizeOf: ' . sizeOf ( $this->allImages ) );
    if ( $this->s [ 'action' ] == 'batch' )
      {
        $this->batchPhotos();
      }
    elseif ( $this->s [ 'action' ] == 'updateDbImageSizes' )
      {
        $this->updateDbImageSizes();
      }

  }

  /*private function updateDbImageSizes()
  {
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $num_updates = 0;
    $updated_ids = array();
    $already_done = 0;
    foreach ( $this->allImages as $k => $v ) 
      {
        if ( ( $v [ 'image_orig_width' ] == 0 ) || ( $v [ 'image_orig_height' ] == 0 ) )
          {
            $id = $v [ 'image_id' ];
            $file = IMG_UPLOAD_DIR . '/' . $id . '.jpg';
            $info = getimagesize ( $file );
            //w and h are max values: new w OR new h should match them: so try
            $q = 'update images set image_orig_width = "' . $info [0] . '", image_orig_height="' . $info[1] . '" where image_id="' . $v ['image_id'] . '"';
            $result = tdb::getInstance()->queryNoResult( $q );
            $num_updates+=1;
            $updated_ids[] = $id;
          }
        else
          {
            $already_done ++;
          }
      }
    if ( $num_updates > 0 ) {
      $msg = 'updateDbImageSizes just updated ' . $num_updates . ' files with w and h sizes in the db';
      $msg .= '<br>images updated were:  ' . implode ( $updated_ids, ',' );
      $msg .= '<br>already done:  ' . $already_done;

      if ( IS_CRON )
        {
          mail ( 'ben@dualism.com.au','Cron img size on Natwalker.com', $msg, $headers );
        }
      else
        {
          echo ( $msg );
        }
    }
    }*/

  private function batchPhotos()
  {
    set_time_limit(0);

    $msg = '<br><br><br> Cron batch starting with IS_CRON: ' . IS_CRON . ', BATCH_ATTEMPT_LIMIT: ' . BATCH_ATTEMPT_LIMIT . ', BATCH_RESIZE_LIMIT: ' . BATCH_RESIZE_LIMIT . '<br> ';
    //    mail ( 'ben@dualism.com.au',$msg, $msg );

    $time_start = microtime( true ); 

    //start a new array that will probably have a false value for height
    //if height if false, then we'll use width for resize, and vice versa
    $resize_dimensions = array ( array ( GALLERY_THUMB_RESIZE_WIDTH, GALLERY_THUMB_RESIZE_HEIGHT ) );
    //screen hero ratios have HEIGHT sizes in their value slots
    //eg '800'=>'720','1080'=>'1024': 720 and 1024 are height
    $ratios = unserialize ( SCREEN_HERO_RATIOS );
    foreach ( $ratios as $k => $v ) {
      //the values ($v) in screen_hero_ratios are HEIGHTS
      //we populate a false for the first slot: WIDTH: thus the heights will be used for this
      $resize_dimensions [] = array ( -1, $v  );
    }

    //now we probably have an array with false for thumb height, and false for the hero widths
    //genFuncs::getInstance()->spit ( $resize_dimensions );

    $newImages = 0;
    $existingImages = 0;

    $ids_were_resized =0;
    $ids_attempted = 0;

    foreach ( $this->allImages as $k => $v ) 
      {
        if ( ( ( ( $ids_attempted < BATCH_ATTEMPT_LIMIT ) || ( BATCH_ATTEMPT_LIMIT == 'all') ) && ( $ids_were_resized < BATCH_RESIZE_LIMIT ) ) || ( BATCH_RESIZE_LIMIT == 'all' ) )
          {
            $ids_attempted++;

            //new method: batch_img_limit will iterate over one image id, each id batched to multiple sizes
            //img object will be responsible for saving each size it creates as a separate db entry in images_resized
            //there will no longer be an option for img to return a dynamically created image on the live site: way too slow
            //only pass the id, and an array with a sub-array for each required dimension
            $id = $v [ 'image_id' ];
            $imageDataResults = img::getInstance()->batchOneImageToAllSizes ( $id, $resize_dimensions );
            if ( $imageDataResults [ 'did_resize' ]  )
              {
                $ids_were_resized++;
              }
          }
        else
          {
            //break if you're over the batch limit
            break;
          }
      }

    $time_end = microtime(true);
    $execution_time = ($time_end - $time_start) / 60;
    $exec_t_formatted = number_format($execution_time, 2, '.', ''); //$execution_time

    $headers = '';
    //$headers = "From: " . strip_tags($_POST['req-email']) . "\r\n";
    //$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $msg .= '<br><br>--------<br>';
    $msg .= 'A CRON job just finished processing.<br><br>';
    $msg .= '<b>' . $ids_attempted . ' image ids attempted to be processed.</b><br>';
    $msg .= '<b>' . $ids_were_resized . ' ids were resized at least once.</b><br>';

    $msg .= '<br>' . $exec_t_formatted . ' minutes to execute.<br>';
    $msg .= '<br><br>GD_AVAILABLE ' . GD_AVAILABLE . '<br>';
    $msg .= 'IMAGICK_AVAILABLE ' . IMAGICK_AVAILABLE . '<br>';

    //    if ( ( IS_CRON ) && ( $newImages > 0 ) )
    if ( IS_CRON )
      {
        mail ( 'ben@dualism.com.au','Cron batch on Natwalker.com', $msg, $headers );
      }
    else
      {
        echo ( $msg );
      }
  }
}
?>