<?php
class formBasic 
{
  private $fname;
  public $fdata;

  public function __construct()
  {
  }

  public function buildAndValidate ( $fname, $action )  
  {
    $this->fname = $fname;
    include ( T_INC_DIR . '/form_data.php' );
    $this->fdata = $this->fdata[$this->fname];
    $this->fdata['name'] = $this->fname;
    $this->fdata['action'] = $action;
    $this->fdata['float'] = ( isset ( $this->fdata['float'] ) ) ? $this->fdata['float'] : 'left';

    if ( $_POST [ $this->fname . '_submitted' ] ) 
      {

        $this->fdata['error_msg'] = false;
        if ( $this->validate() )
          {
            return true;        
          }
        else 
          {
            $this->showForm();
          }
      }
    else 
      {
        $this->showForm();
      }
    //   return $this->fdata;
    // }
  }

  private function validate()
  {
    $valid = true;
    foreach ( $this->fdata [ 'elements' ] as $k => $v ) 
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

  public function showForm() 
  {
    $this->getLabels ();
    // echo ( $this->content_templates [ 1 ] );
    // echo ( '<br>' . $this->main_include );
    if ( $this->main_include ) {
      include ( $this->main_include );
    }
  }

  private function getLabels () {
    foreach ( $this->fdata['elements'] as $k => $v ) 
      {
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


}
?>