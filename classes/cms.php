<?php

class cms {
  private $s = array();
  private $form = array();
  private $tpl_content;
  // private $fdata;
  private $fname;
  public $folios = array();
  public $galleryImages = array();
  public $allImages = array();
  public $content_templates = array();
  private $upload_error = array();
  private $confirm_msg = array();
  public $cur_gallery_id;
  public $switch_link;
  public $jumpToId;

  public $galleryList = array();
  public $cur_section_name;

  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->section;
    $this->switch_link = '<a href="' . T_HOME_URL . '">&lt; View Site</a>';
    $this->content_templates[0] = T_TEMPLATES_DIR . '/cms_menu.html';
  }

  public function showCms ()
  {

    if ( $this->s [ 'subsect' ] [ 'section_name' ] == 'photos' )
      {
        $this->goPhotos();
      }
    elseif ( $this->s [ 'subsect' ] [ 'section_name' ] == 'batchphotos' )
      {
        $this->batchPhotos();
      }

    include ( T_TEMPLATES_DIR . '/cms.html' );
  }

  private function goPhotos() 
  {

    $sections = tdb::getInstance()->getAll( 'sections', 'section_level', '1', 'order_num' );
    array_pop ( $sections );

    foreach ( $sections as $k=>$v ) {
      $subsections = tdb::getInstance()->getAll( 'sections', 'parent_id', $v [ 'section_id' ], 'order_num' );
      array_pop ( $subsections );
      if ( sizeOf ( $subsections) > 0  ) {
        $this->galleryList [ $v [ 'section_name' ] ]  [ 'section_name' ] = $v [ 'section_name' ];
        $this->galleryList [ $v [ 'section_name' ] ] [ 'subsects' ] = array();

        foreach ( $subsections as $k2=>$v2 ) {
          if ( $v2 [ 'page_type_id' ] == '2' ) {
            $this->galleryList [ $v [ 'section_name' ] ] [ 'subsects' ] []  =  $v2;
          }
        }
      }
    }
    //    $this->galleryList

    $this->content_templates[1] = T_TEMPLATES_DIR . '/photos.html';

    if ( isset ( $_POST ['gallery_id'] ))
      {
        $this->processUpload();
      }
    $this->showFolio();
  }
  
  private function showFolio()
  {
    $this->switch_link = '<a href="' . T_HOME_URL . 'folio">&lt; View Site</a>';
    if ( $this->s [ '2' ]  && !$this->s [ '3' ] )
      {
        $this->switch_link = '<a href="' . T_HOME_URL  . $this->s [ '2' ] . '">&lt; View Site</a>';
        $this->cur_section_name = $this->s ['2'];
      } else if ( $this->s [ '3' ] ) 
      {
        $this->switch_link = '<a href="' . T_HOME_URL  . $this->s [ '2' ] . '/' . $this->s [ '3' ]  . '">&lt; View Site</a>';
        $this->cur_section_name = $this->s ['3'];
      }

    $gallery_row = tdb::getInstance()->getRow ( 'sections','LOWER(section_name)', $this->cur_section_name );
    $this->cur_gallery_id = $gallery_row [ 'section_id' ];
    $this->galleryImages = tdb::getInstance()->getAll( 'images','section_id',$this->cur_gallery_id, 'order_num', 'asc');

    if ( $this->s [ '4' ] > 0 )
      {
        $this->jumpToId = $this->s [ '4' ];
        $this->switch_link = '<a href="' . T_HOME_URL  . $this->s [ '2' ] . '/-1/' . $this->s [ '3' ] . '">&lt; View Site</a>';
      }
  }

  private function processUpload()
  {
    $types =  preg_split('/:/', IMG_ALLOWED_TYPES, -1, PREG_SPLIT_NO_EMPTY); 

    $images = array();
    foreach ($_FILES['files'] as $key => $value) {
      foreach($value as $index => $val){
        $images[$index][$key] = $val;
      }
    }

    foreach ( $images as $index=>$cur_array )
      //start mutliple images loop
      {
        //        echo ( '<br>: ' . $index );
        //$files is one $_FILE array

        // echo ( '<br> array[name]: ' . $cur_array [ 'name' ] );
        // echo ( '<br> array[size]: ' . $cur_array [ 'size' ] );

        //start single image code
        $right_type = false;

        foreach ( $types as $tk=>$tv )
          {

            if ( $cur_array["type"] == $tv )
              {
                $right_type = true;
                break;
              }
          }
        if ( !$right_type ) 
          {
            $this->upload_error [ $index ]  = 'Error - ' . $cur_array [ 'name' ] . ' is the wrong type of file';
          }

        $sizeKb = $cur_array["size"] / 1024;
        if ( $sizeKb  > IMG_MAX_SIZE )
          {
            $this->upload_error [ $index ] .= 'Error - ' . $cur_array [ 'name' ] . ' is larger than the maximum size of ' . $sizeKb . ' Kb';
          }
        if ($cur_array["error"] > 0)
          {
            if ( $cur_array["error"] == 4 ) 
              {
                $this->upload_error [ $index ] .= 'Error: no file submitted';
              }
            else
              {
                $this->upload_error [ $index ] .= 'Error - ' . $cur_array["name"] . ': ' . $cur_array["error"] . '';
              }
          }
        //$q = 'SELECT MAX( order_num) as order_num FROM images where section_id="' . $_POST['gallery_id'] . '"';
        $q = 'select order_num, section_id from images where section_id="' . $_POST['gallery_id'] . '" order by order_num desc limit 1';
        $max_order_row = tdb::getInstance()->query ( $q );
        $max_order = $max_order_row [ 'order_num' ];
        $new_order = $max_order + 1;

        if ( $this->upload_error [ $index ] == false )
          {
            $info = getimagesize ( $cur_array["tmp_name"] );
            $q = 'insert into images set active_status="1", section_id="' . $_POST['gallery_id'] . '", order_num="' . $new_order . '", image_orig_width="' . $info[0] . '", image_orig_height="' . $info[1] . '"';
            //            genFuncs::getInstance()->spit ( 'q: ' . $q );
            tdb::getInstance()->queryNoResult ( $q );

            if ( move_uploaded_file( $cur_array["tmp_name"], IMG_UPLOAD_DIR . '/' . tdb::getInstance()->ob->insert_id . '.jpg' ) )
              {
                $this->confirm_msg [ $index ] = $cur_array [ 'name' ] . ' uploaded successfully'; 
              }
            else 
              {
                $this->upload_error [ $index ] = 'There was a problem uploading image/s. Please try again.';
              }
          }
            
      }//end mutliple images loop
  }//end process upload
}

?>