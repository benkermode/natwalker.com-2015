<?php

class site {
  public $navs = array();
  public $s = array();
  private $defaultSubject = array();
  //  public $folio;
  public $switch_link;
  public $contact;
  public $content_templates = array();
  public $navCase;
  public $ieVersion;
  public $detect;
  public $deviceType;
  public $scriptVersion;

  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->section;
    $this->switch_link = '<a href="' . DEFAULT_CMS_PAGE . '">Edit Site &gt;</a>';
    $this->ieVersion = browser::getInstance()->ieVersion;

  }

  public function showSite()
  {

    $table = 'sections';
    $order = 'asc';
    //build sub-menu first so default section can be included in main menu
    $num_subsects = tdb::getInstance()->getRows ( 'sections', 'parent_id', $this->s [ 'section_id' ] );

    if ( $num_subsects > 0 ) {

      $parent_id = $this->s [ 'section_id' ];

      $section_level = '2';
      $base_url = T_HOME_URL . $this->s [ 'section_name' ] . '/';
      $url_match = $this->s [ 'subsect' ] [ 'section_name' ]; 
      $this->navs [ $this->s [ 'section_name' ] ] = $this->buildNav ( $section_level, $table, $order, $base_url, $url_match, $case, $parent_id );

    }

    //build Main Nav
    $section_level = '1';
    $base_url = T_HOME_URL;
    $url_match = $this->s [ 'section_name' ]; 
    $this->navs [ 'main' ] = $this->buildNav ( $section_level, $table, $order, $base_url, $url_match, $case, false );

    $this->checkSectionType ();
    $this->switch_link = '<a href="' . DEFAULT_CMS_PAGE . '">Edit Site &gt;</a>';
    //$this->content_templates[1] = T_TEMPLATES_DIR . '/folio_thumbs.html';
    $this->content_templates[0] = T_TEMPLATES_DIR . '/' . $this->s [ 'section_name' ] . '.html';

    // genFuncs::getInstance()->spit ( $this->content_templates );
    include ( T_TEMPLATES_DIR . '/index.html' );
  }

  private function checkSectionType () {

    //check if subsect is set at all... if so, check its page type, if not, check section page type

    $page_type_to_check = ( $this->s [ 'subsect' ] ? $this->s [ 'subsect' ] [ 'page_type_id' ] : $this->s [ 'page_type_id' ] );

    $q = 'select page_type_name from page_types where page_type_id = "' .  $page_type_to_check . '"' ;
    $page_type = tdb::getInstance()->query ( $q );
    //    genFuncs::getInstance()->spit ( 'page type: ' . $page_type [ 'page_type_name' ] );

    if ( $page_type [ 'page_type_name' ] == 'gallery' ) {
      $this->gallery = new galleryDisplay();
      $this->gallery->showGallery();
      $this->switch_link = $this->gallery->switch_link;
    }

  }

  public function buildNav ( $section_level, $table, $order, $base_url, $url_match, $case, $parent_id )
  { 
    $case = $this->navCase;
    //change to return an array - do formatting in html
    $nav = array ();
    if ( $parent_id )
      {
      $result = tdb::getInstance()->getAll ( $table,'section_level', $section_level, 'order_num', $order, false, false, 'parent_id', $parent_id );
    }
    else
      {
      $result = tdb::getInstance()->getAll ( $table,'section_level', $section_level, 'order_num', $order  );
    }

    array_pop ( $result );

    $singular_table = substr ( $table, 0, -1 );

    foreach ( $result as $k=>$v ) {
      $name = $v [ $singular_table . '_name' ];

      if ( strlen ( $name ) > 0 )
        {

          $nav [ $k ] = array();
          $nav [ $k ] [ 'name' ] = strtoupper ( $name );
          
          if ( $case == 'camel' ) {

            $nav [ $k ] [ 'name' ] = ucwords ( $name );

          } elseif ( $case == 'lower' ) {

            $nav [ $k ] [ 'name' ] = strtolower ( $name );

          }

          if ( $url_match == strtolower( $name ) ) {

            $nav [ $k ] [ 'selected' ] = 'selected';
            $nav [ $k ] [ 'link' ] = '';

          } else {

            $nav [ $k ] [ 'selected' ] = '';
            $nav [ $k ] [ 'link' ] = $base_url  . strtolower( $name  );

            //check for default subsect
            if ( $section_level == '1' ) {

              $q = 'select * from sections where parent_id = "' . $v [ 'section_id' ] . '" order by order_num limit 1';

              $cur_default_sub = tdb::getInstance()->query ( $q );
              if ( is_array ( $cur_default_sub ) ) {
                array_pop ( $cur_default_sub );
                if ( $cur_default_sub [ 'section_name' ] ) {
                  $nav [ $k ] [ 'link' ] .= '/' . $cur_default_sub [ 'section_name' ];
                }
              }
            }
          }
        }

      if ( empty ( $nav [ $k ] [ 'name' ] )  ) {
        unset ( $nav [ $k ] );
      }
    }
    return $nav;
  }
}