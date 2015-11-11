<?php

class loginOb extends formBasic  {
  private $s = array();
  private $form = array();
  private $tpl_content;
  // private $fdata;
  private $fname;
  public $content_templates = array();
  public $main_include;

  public function __construct ( ) {
    parent::__construct();
    $this->s = sectionsOb::getInstance()->section;
    $this->fname = 'login';
    $this->content_templates[0] = false;
    $this->content_templates[1] = T_TEMPLATES_DIR . '/form.html';
    $this->main_include = T_TEMPLATES_DIR . '/cms.html';
  }

  public function checkLogin ()
  {
    if ( $this->s [ 'section_name' ] == 'login' )
      {
        if ( $this->buildAndValidate ( $this->fname, T_HOME_URL . 'login' ) )
          {
            $this->processForm();
          }
      }
    elseif ( $this->s [ 'section_name' ] == 'logout' )
      {
        //        unset ( $_SESSION['user'] );
        unset ( $_SESSION [ 'user' ] );
        unset ( $_SESSION [ 'screen' ] );
        header('Location: ' . T_HOME_URL . 'login' );
      }
  }

  private function processForm() 
  {

    $hash_predb =   tdb::getInstance()->getHash ( $_POST['password'] );
    $row = tdb::getInstance()->getRow ( 'user', 'email', $_POST['email'] );
    //echo $hash_predb . '<br>vs:<br>' . $row [ 'password' ];
    //pasted the returned hash value in phpmyadmin - needed strip slashes in the tdb class

    if ( $hash_predb  == $row['password'] )
      {
        //        genFuncs::getInstance()->spit ( 'match' );
        session_regenerate_id(true);
        $_SESSION['user'] = $row;
        unset( $_SESSION['user']['password'] );

        if ( $_SESSION['user']['access'] == '2' ) 
          {
            unset ( $_POST );
            header('Location: ' . DEFAULT_CMS_PAGE );
          }
      }
    else
      {
        genFuncs::getInstance()->spit ( 'unmatch' );
        foreach ( $this->fdata['elements'] as $k => $v ) 
          {
            $this->fdata['elements'][$k]['error'] = true;
            $this->fdata['elements'][$k]['class'] = 'error';
          }
        $this->showForm();
      }
  }
}

?>