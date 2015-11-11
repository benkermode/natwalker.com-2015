<?php

class sectionsOb {
  private static $instance = __CLASS__;
  public $section = array();


  //manually add only unchaging sections
  private $allowed_sections = array();
  private $allowed_subsects = array();

  public static function getInstance () {
    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
  }

  public function __construct () { 
  }

  public function handleRequests() {

    //add sections to allowed_sections    
    $sections = tdb::getInstance()->getAll ( 'sections', 'section_level','1','order_num', 'asc' );
    foreach ( $sections as $k=>$v ) {
      $this->allowed_sections [] = $v [ 'section_name' ];
    }
    array_pop ( $this->allowed_sections ); //remove empty last item

    //add hidden sections to allowed_sections    
    $hidden = tdb::getInstance()->getAll ( 'sections', 'section_level','-1','order_num', 'asc' );
    array_pop ( $hidden ); //remove empty last item
    foreach ( $hidden as $k=>$v ) {
      $this->allowed_sections [] = $v [ 'section_name' ];
    }

    //check GET [0]    
    if ( $_GET['g0'] == 'ajax' ) {

      //do nothing so url can use .getjson
      $this->section ['section_name'] = 'ajax';

    } elseif ( ( $_GET['g0'] == 'cron' ) || ( $_GET [ 1 ] == 'cron' ) ){

      //$_SERVER ['argv'] [0] is the script that calls the cron job

      //when running a CRON job, $_GET [ 1 ], , $_GET [ 2 ], , $_GET [ 3 ] are set from $argv via definitions.php
      $g1 = ( isset ( $_GET['g1'] ) ) ? $_GET['g1'] : $_GET [ 2 ];
      $g2 = ( isset ( $_GET['g2'] ) ) ? $_GET['g2'] : $_GET [ 3 ] ;


      $this->section ['section_name'] = 'cron';
      if ( ( $g1 == 'batch' ) && ( $g2 == CRON_BATCH_PASSWORD ) )
        {
          $this->section ['action'] = 'batch';
        }
      elseif ( $g1 == 'updateDbImageSizes' ) 
        {
          $this->section ['action'] = 'updateDbImageSizes';
        }

    } elseif ( !isset( $_GET['g0'] ) ) {

      $this->section[ 'section_name' ] = 'home';
      $this->is_faulty_getvar = false;

    } else {


      if ( in_array (  $_GET['g0'] , $this->allowed_sections ) ) {

        //      genFuncs::getInstance()->spit ( 'sub name: ' + $this->s [ 'subsect' ] [ 'section_name' ] );
        $this->section = tdb::getInstance()->getRow ( 'sections', 'section_name', $_GET['g0'] );

      }

      if ( $_GET['g1'] ) {

        //only allow the subsects for the current section
        $result = tdb::getInstance()->getAll ( 'sections', 'parent_id', $this->section [ 'section_id' ] );

        foreach ( $result as $k=>$v ) {
          $this->allowed_subsects [] =   $v [ 'section_name' ];
          if ( $_GET['g1'] == $v ['section_name'] ) {
            $this->section['subsect'] = tdb::getInstance()->getRow ( 'sections', 'section_id', $v[ 'section_id' ] );
          }
        }

      }
    }

    if ( isset( $_GET['g2'] ) ) {

      $this->section['2'] = $_GET['g2'];

    }

    if ( isset( $_GET['g3'] ) ) {

      $this->section['3'] = $_GET['g3'];

    }
    if ( isset( $_GET['g4'] ) ) {
      $this->section['4'] = $_GET['g4'];
    }

  }

}
?>