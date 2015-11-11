<?php

define ( 'T_ABS_PATH', str_replace('\\', '/', dirname(__FILE__)) );
require_once(T_ABS_PATH . '/includes/definitions.php');

class titan {
  public $db;
  public $errorLog;	
  private $reqs;
  private $page;
  private $s;
  private $sectionsOb;
  private $form;
  private $cms;
  private $cron;
  private $site;
  private $ieVersion;
  private $detect;
  private $deviceType;
  private $scriptVersion;

  public function __construct()
  {
    //prototypes
  }

  public function startup () {
    require_once ( 'classloader.php' );
    smartClassMapper::getInstance();
    spl_autoload_register(array('smartClassMapper','autoloadClass'));

    sectionsOb::getInstance()->handleRequests();
    $this->s = sectionsOb::getInstance()->section;

    browser::getInstance();

    Require_once T_CLASS_DIR . '/Mobile_Detect.php';
    $this->detect = new Mobile_Detect;
    $this->deviceType = ($this->detect->isMobile() ? ($this->detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    $this->scriptVersion = $this->detect->getScriptVersion();

    if ( T_USE_DB ) { 
      tdb::getInstance();
    }

    if ( ( $this->s[ 'section_name' ] == 'login' ) || ( $this->s[ 'section_name' ] == 'logout' ) )
      {
        $this->login = new loginOb ();
        $this->login->checkLogin();
      }
    //all image resizing is done via cron calls, eg http://localhost/wip/nw2015/cron/batch/[cron-password]
    elseif ( $this->s[ 'section_name' ] == 'cron' ) 
      {
            $this->cron = new cron ();
      }
    elseif ( $this->s[ 'section_name' ] == 'cms' ) 
      {
        if ( $_SESSION['user']['access'] == '2' ) 
          {
            $this->cms = new cms ();
            $this->cms->showCms();
          }
        else 
          {
            header('Location: ' . T_HOME_URL . 'login' );
          }
      }
    elseif ( $this->s[ 'section_name' ] == 'ajax' ) 
      {
        $this->ajax = new ajax ();
        $this->ajax->deviceType = $this->deviceType;
        $this->ajax->processRequest();
      }
    else
      {

        if ( !DISABLE_FRONT_END )
          {
            $this->site = new site ();
            $this->site->navCase = 'upper';

            $this->site->detect = $this->detect;
            $this->site->deviceType = $this->deviceType;
            $this->site->scriptVersion = $this->scriptVersion;

            $this->site->showSite();
          }

      }
  }
}


session_start();
// ob_start();
// ob_implicit_flush(0);
$titan = new titan();
$titan->startup();

?>