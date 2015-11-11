<?php

class cms {
  private $s = array();
  private $form = array();
  private $tpl_content;
  // private $fdata;
  private $fname;
  public $folios = array();
  public $galleryImages = array();
  public $content_templates = array();
  private $upload_error;
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
    include ( T_TEMPLATES_DIR . '/cms.html' );
  }

  private function goPhotos() 
  {

    $sections = tdb::getInstance()->getAll( 'sections', 'section_level', '1', 'order_num' );
    array_pop ( $sections );

    foreach ( $sections as $k=>$v ) {
      //      echo '----------<br>';
      //      echo $v [ 'section_name' ] . '<br>';
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
    $this->upload_error = false;
    $right_type = false;
    $types =  preg_split('/:/', IMG_ALLOWED_TYPES, -1, PREG_SPLIT_NO_EMPTY); 

    echo ( '<br>sizeOf: ' . sizeOf ( $_FILES [ 'images' ]) );

    foreach ( $_FILES["images"] as $k=>$v )
      {
        echo ( '<br>images: ' . $k . ': ' . $v );
      }


    foreach ( $types as $k=>$v )
      {

        if ($_FILES["image0"]["type"] == $v )
          {
            $right_type = true;
            break;
          }
      }
    if ( !$right_type ) 
      {
        $this->upload_error = 'Error - That image is the wrong type of file';
      }
    $sizeKb = $_FILES["image0"]["size"] / 1024;
    if ( $sizeKb  > IMG_MAX_SIZE )
      {
        $this->upload_error = 'Error: That image larger than the maximum size of ' . $sizeKb . ' Kb';
      }
    if ($_FILES["image0"]["error"] > 0)
      {
        if ( $_FILES["image0"]["error"] == 4 ) 
          {
            $this->upload_error = 'Error: no file submitted.';
          }
        else
          {
            $this->upload_error = 'Error: ' . $_FILES["image0"]["error"];
          }
      }

    if ( $this->upload_error == false )
      {
        $q = 'insert into images set active_status="1", section_id="' . $_POST['gallery_id'] . '"';
        tdb::getInstance()->queryNoResult ( $q );
        //genFuncs::getInstance()->spit ( 'post q: ' . $q);

        if ( move_uploaded_file( $_FILES["image0"]["tmp_name"], IMG_UPLOAD_DIR . '/' . tdb::getInstance()->ob->insert_id . '.jpg' ) )
          {
            $this->confirm_msg = 'Image uploaded successfully'; 
          }
        else 
          {
            $this->upload_error = 'There was a problem uploading your image. Please try again.';
          }
      }
    //    echo "Upload: " . $_FILES["image0"]["name"] . "<br />";
  }
}

?>