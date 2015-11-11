<?php

class form  {
  private $sections = array();
  public $fdata;
  public $errors;
  public $fname;

  public function __construct( $source )
  {
    echo $source;
  }

  public function buildAndValidate ( $fname, $action )  
  {
    echo 'build and val';
    $this->fname = $fname;
      include ( T_INC_DIR . '/form_data.php' );
      $this->fdata = $this->fdata[$this->fname];
      $this->fdata['name'] = $this->fname;
      $this->fdata['action'] = $action;
      $this->fdata['float'] = ( isset ( $this->fdata['float'] ) ) ? $this->fdata['float'] : 'left';
    //   $this->getLabels ();

    //   if ( $_POST [ $this->fname . '_submitted' ] ) {
    //     $this->fdata['error_msg'] = false;
    //     if ( $this->validate() )
    //       {
    //         $this->processForm();
    //       }
    //   }
    //   return $this->fdata;
    // }
  }


  private function validate()
  {
    //genFuncs::getInstance()->spit ( $_POST );
    $valid = true;
    foreach ( $this->fdata['elements'] as $k => $v ) 
      {
        if ( $_POST[$k] == '' && $v['mand'] )
          {
            $this->fdata['elements'][$k]['error'] = true;
            $this->fdata['elements'][$k]['class'] = 'error';
            $valid = false;
          }
      }
    return $valid;
  }

  private function getLabels () {
    echo 'getlables';
    foreach ( $this->fdata['elements'] as $k => $v ) 
      {
        echo '<br>' . $k . ' ' . $v;
        if ( ! $v['class'] )
          {
            $this->fdata[$k]['class'] = '';
          }
        if ( ! isset ( $v['label'] ) )
          {
            $l = explode ('_',$k);
            foreach ( $l as $kk=>$vv ) 
              {
                $l[$kk] = ucfirst ( $vv );
              }
            $this->fdata['elements'][$k]['label'] = implode (' ', $l );
          }

      }
  }

  private function showThankyou () {
  }
  private function sendEmail () {
  }
  private function getTemplate ( $which ) 
  {
    $which = ( $which ) ? $which : $this->gf->getSectionsString();
    $file = T_TEMPLATES_DIR . '/' . $which . '.html';
    return $file;
  }

}
?>
  
