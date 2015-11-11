<?php

class contactForm extends formBasic  {
  private $s = array();
  private $form = array();
  private $tpl_content;
  // private $fdata;
  private $fname;
  public $content_templates = array();
  public $main_include;
  public $sent;
  private $recipient;

  public function __construct (  ) {
    parent::__construct();
    $this->s = sectionsOb::getInstance()->sections;
    $this->fname = 'contact';
    $this->content_templates[1] = T_TEMPLATES_DIR . '/contact.html';
    $this->main_include =  null;//T_TEMPLATES_DIR . '/cms.html';
    $this->sent = false;
  }

  public function goContact() 
  {
    if ( $this->s [ 'section' ] == 'contact' )
      {
        if ( $this->buildAndValidate ( $this->fname, T_HOME_URL . 'contact' ) )
          {
            $this->processForm();
          }
      }
  }

  private function processForm() 
  {
    $this->sendEmail();
    $this->sent = true;
  }

  public  function sendEmail () 
  {
    $msg = '';
    foreach ( $this->fdata[ 'elements' ] as $k => $v ) 
      {
        if ($v[2] ) 
          {
            $label = $v[2];
          }
        else 
          {
            $l = explode ('_',$k);
            foreach ( $l as $kk=>$vv ) 
              {
                $l[$kk] = ucfirst ( $vv );
              }
            $label = implode (' ', $l );

          }
        // echo $k . '<br>';      
        // echo 'v: ' . $v . '<br>';      
        // echo 'v2: ' . $v [2] . '<br>';      
        // echo 'v0: ' . $v [0] . '<br>';      
        // echo 'v1: ' . $v [1] . '<br>';      
        // echo 'label: ' . $label . '<br>';
        // echo 'post k: ' . $_POST [ $k ]  . '<br>';
        $msg .= $label . ':
' . $_POST [ $k ] . '
' . "\n";

         // echo $msg;
      }

    // genFuncs::getInstance()->spit ( $msg );

    $recips =  preg_split('/:/', CONTACT_RECIPIENT, -1, PREG_SPLIT_NO_EMPTY); 
    foreach ( $recips as $k=>$v )
      {
        // mail ( $v, CONTACT_SUBJECT, $msg, 'From: "' . ADMIN_EMAIL . '" <' . ADMIN_EMAIL . '>');
        mail ( $v, CONTACT_SUBJECT, $msg, 'From: "' . $_POST [ 'name_first' ] . ' ' . $_POST [ 'name_last' ]  . '" <' . $_POST [ 'email' ] . '>');
      }
  }

}

?>