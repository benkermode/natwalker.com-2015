<?php

class ajax {
  public $result;
  public $jsonSend = array();
  public $deviceType;
  public function __construct ( ) {
  }

  public function processRequest()
  {

    //post data objects coming from angular are available from $params using the code below
    $params = json_decode(file_get_contents('php://input'),true);

    if ( $_POST [ 'action' ] == 'setScreenVars' )
      {
        $this->setScreenVars();

      } 
    elseif ( $_POST [ 'action' ] == 'loadHero' )

      {
        $this->loadHero();

      } 
    elseif ( $_POST [ 'action' ] == 'loadThumb' )

      {
        $this->loadThumb();

      }  
    elseif ( $_POST [ 'action' ] == 'setScreenVars' )

      {
        $this->setScreenVars();
      }
    //this one sent from angular, use $params
    elseif ( $params [ 'action' ] == 'getSiteData' )
      {
        $this->getSiteData();
      }
    elseif ( $_POST [ 'action' ] == 'contactFormSubmit' )
      {
        $this->contactFormSubmit();
      }
    else 
      {

        if ( $_SESSION['user']['access'] == '2' ) {
          if ( $_POST [ 'action' ] == 'sortUpdate' )
            {
              $this->sortUpdate();
            }
          elseif ( $_POST [ 'action' ] == 'imgStatusUpdate' )
            {
              $this->imgStatusUpdate();
            }
          elseif ( $_POST [ 'action' ] == 'deleteImage' )
            {
              $this->deleteImage();
            }
          elseif ( $_POST [ 'action' ] == 'saveCaption' )
            {
              $this->saveCaption();
            }
        }
        else 
          {
            //no action requested, just notifify this file being accessed
            echo ( 'ajax file' );
          } 
      }

  }

  public function setScreenVars()
  {
    //this WON'T be set during the first page load
    // $_SESSION[ 'screen' ] = array();
    // $_SESSION['screen']['width'] = $_POST [ 'screenAr' ] [ 0 ];
    // $_SESSION['screen']['height'] = $_POST [ 'screenAr' ] [ 1 ];

    // $this->jsonSend [ 'success ' ] = '1';
    // $this->jsonSend [ 'screenWidth ' ] = $_POST [ 'screenAr' ] [ 0 ];
    // $this->jsonSend [ 'screenHeight ' ] = $_POST [ 'screenAr' ] [ 1 ];
    //this WON'T be set during the first page load
    $_SESSION[ 'screen' ] = array();
    $sw = $_POST [ 'screenW' ];
    $sh = $_POST [ 'screenH' ];
    $_SESSION['screen']['width'] = $sw;
    $_SESSION['screen']['height'] = $sh;

    $targetDim = $sh;
    if ( ( $this->deviceType == 'phone' ) || ( $this->deviceType == 'tablet' ) )
      {
        $targetDim = ( $sw > $sh ) ? $sw : $sh; 
      }
    $_SESSION['screen']['targetScreenDimension'] = $targetDim;

    $this->jsonSend [ 'targetScreenDimension' ] = $_SESSION ['screen'] [ 'targetScreenDimension' ];
    $this->sendJson();
  }

  public function deleteImage()
  {
    $id = $_POST [ 'id' ];
    $delete_original = IMG_UPLOAD_DIR . '/' . $id . '.jpg';
    $this->jsonSend [ 'success'] = '0';
    if ( file_exists ( $delete_original ) )
      {
        if ( unlink ( $delete_original ) )
          {
            $this->jsonSend [] = 'DELETED: ' . $delete_original;
            $q = 'delete from images where image_id="' . $id . '"';
            tdb::getInstance()->queryNoResult ( $q );
            $this->jsonSend [ 'success'] = '1';
            $this->jsonSend [ 'q '] = $q;
          }
      }
    
    $dir = ABS_RESIZED_DIR . '/*';

    foreach( glob($dir) as $file)  
      {  
        if ( filetype( $file ) == 'dir' )
          {
            $delete_file = $file . '/' . $id . '.jpg';
            if ( file_exists ( $delete_file ) )
              {
                unlink ( $delete_file );       
              }
          }
      }  
    $this->sendJson();
  }

  public function loadHero()
  {
    $ratios = unserialize ( SCREEN_HERO_RATIOS );
    //$this->jsonSend [ 'ratios_length' ] = sizeOf ( $ratios );

    foreach ( $ratios as $k => $v ) 
      {
        //        if ( $k >= $_SESSION [ 'screen' ] [ 'height' ] ) {
        //in ajax, we use the javascript screen height, not the one in the session variable
        if ( $k >= $_POST [ 'screenH' ]  ) {
          $desired_height = $v;
          break;
        }
      }
    //    $this->jsonSend [ 'hero' ] = img::getInstance()->getImage ( $_POST [ 'id' ], false, $desired_height, 'class="hero" id="hero_' . $_POST [ 'id' ] . '"' );

    $this->jsonSend [ 'hero' ] = img::getInstance()->getImage ( $_POST [ 'id' ], false, $desired_height, '','array' );
    $this->sendJson();
  }

  public function loadThumb()
  {
    $this->jsonSend [ 'thumb' ] = img::getInstance()->getImage ( $_POST [ 'id' ], false, $_POST [ 'h' ], '', 'array' );
    //pass back the index to the handler
    $this->jsonSend [ 'index' ] = $_POST [ 'index' ];
    $this->sendJson();
  }

  public function saveCaption()
  {
    $this->jsonSend [ 'action' ] = 'saveCaption';
    $q = 'update images set image_caption = "' . $_POST [ 'val' ] . '" where image_id ="' . $_POST [ 'id' ] . '"';
    $this->jsonSend [ 'success' ] = '0';
    tdb::getInstance()->queryNoResult ( $q );
    //mysql_affected_rows returns -1 for a non-result
    if ( mysql_affected_rows() > -1     )
      {
        $this->jsonSend [ 'success' ] = '1';
      }
    //whatever happens, make sure we display the caption as it currently stands in the db
    $saved_row = tdb::getInstance()->getRow ( 'images', 'image_id', $_POST [ 'id' ]  );
    $this->jsonSend [ 'savedCaption' ] = $saved_row [ 'image_caption' ];
    $this->sendJson();
  }

  public function imgStatusUpdate()
  {
    $this->jsonSend [ 'action' ] = 'imgStatusUpdate';
    $q = 'update images set active_status = "' . $_POST [ 'imgData' ][ 1 ] . '" where image_id ="' . $_POST [ 'imgData' ][ 0 ] . '"';
    tdb::getInstance()->queryNoResult ( $q );
    $this->jsonSend [ 'q' ] = $q;
    $this->sendJson();
  }

  public function sortUpdate()
  {
    foreach ( $_POST [ 'nuOrder' ] as $k=>$v )
      {
        $id = substr ( $v, 5 );
        $q = 'update images set order_num="' . $k . '" where image_id="' . $id . '"';
        tdb::getInstance()->queryNoResult ( $q );
        $this->jsonSend [ $k ] = $q;
      }
    $this->sendJson();
  }

  public function contactFormSubmit() 
  {
    $site = 'NatWalker.com';
    $from = 'form@natwalker.com';
    $subject = 'Contact from ' . $site;
    $body = 'Email: ' . $_POST ['email'] . '

Name: 
' . $_POST [ 'name' ] . '

Phone: 
' . $_POST [ 'phone' ] . '

Message: 
' . $_POST [ 'message' ];
    $headers = "From: $from\r\nReply-to: " . $_POST [ 'email' ];

    // mail('ben@dualism.com.au', $subject, $body, $headers);
    // $sent = mail( 'info@natwalker.com', $subject, $body, $headers);

    $sent = mail('ben@dualism.com.au', $subject, $body, $headers);

    $this->jsonSend [ 'email_sent' ] = $sent;

    $this->sendJson();
  }

  public function getSiteData ()
  {
    //get the SECTIONS (parent id of -1 is not specified and 11 is for the CMS )
    //sections selected in order with all top level sections ordered first, then all 2nd level sections next
    $q = 'SELECT section_id, section_name, section_level, section_id, parent_id, order_num, active FROM sections where page_type_id="1" or page_type_id="2" order by section_level, order_num';
    $sections = tdb::getInstance()->getFromQ ( $q );
    array_pop ( $sections );
    $sections_hierachy = array();
    //$sectionIndexById is an associative array where you can find the index of a section in the $secions array by using the section_id as the array key 
    $sectionIndexById = array();
    foreach ( $sections as $k => $v ) {
      //if this is a top level section / menu item
      if ( $v [ 'section_level' ] == '1' ) {
        //store its index in $sections_hierachy so it can be called using it's section_id as a key
        $sectionIndexById [ $v ['section_id'] ] = $k;
        //this section goes straight in the final array
        $sections_hierachy [ $k ] = $v;
      } elseif ( $v [ 'section_level' ] == '2' ) {
        //get the index of this sections parent in $sections_hierachy by using this sections PARENT_ID (corresponds to the section_id stored above)
        $parent_index = $sectionIndexById [ $v [ 'parent_id' ] ];
        if ( !$sections_hierachy [ $parent_index ] [ 'subsections' ] ) {
          //create the array if it doesn't exists
          $sections_hierachy [ $parent_index ] [ 'subsections' ] = array();
        }
        //store the subsection
        $sections_hierachy [ $parent_index ] [ 'subsections' ] [] = $v;
      }
    }
    $this->jsonSend [ 'sections' ] = $sections_hierachy;

    //get the IMAGE DATA
    $q = 'select images.image_id, images.section_id, images.order_num, images.image_orig_width, images.image_orig_height, images.image_has_sizes, sections.section_name from images inner join sections on sections.section_id=images.section_id where active_status="1" order by section_name, images.order_num limit 0,9999';
    $images = tdb::getInstance()->getFromQ ( $q );
    array_pop ( $images );

    $this->jsonSend [ 'image_ids' ] = array();
    foreach ( $images as $k=>$v )
      {
        //$this->jsonSend [] = array ( $v [ 'image_id'], $v ['image_orig_width'], $v ['image_orig_height'], $v [ 'image_has_sizes'], $v [ 'section_name'] );
        $this->jsonSend [ 'image_ids' ] [] = array ( $v [ 'image_id'], $v [ 'image_has_sizes'], $v [ 'section_name'] );
      }

    $this->sendJson();
  }


  public function sendJson()
  {
    //json
    header('Access-Control-Allow-Origin: *'); 
    header ( 'Content-Type: application/json' );
    //two options for text
    //header ( 'Content-Type: text/plain' );
    //header ( 'Content-Type: text/html' );
    //xml tree
    // header ( 'Content-Type: text/xml' );

    // echo json_encode ( $this->jsonSend, true );
    echo json_encode ( $this->jsonSend );

    exit;
  }

}
?>
