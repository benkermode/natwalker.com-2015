<?
// echo $_SERVER['HTTP_HOST'] . '<br>';
// DB login name with webhost: Lise7x0!
//grant all privileges on dualism2_nw2014.* to dualism2_nw2014@'localhost' identified by '7WYUBLQjj9HezmSC' WITH GRANT OPTION;
//required for seding arguments to cron job
if ( $argv )
  {
    foreach ( $argv as $k=>$v ) {
      $_GET [ $k ] = $v;
    }
  }
if ( $_GET [ 1 ] == 'cron' )
  {
    define('IS_CRON', true);
  }
else
  {
    define('IS_CRON', false);
  }

define('MAGICK_PATH', '/Applications/MAMP/bin/ImageMagick/ImageMagick-6.8.8/bin/convert');

define('T_USE_DB',true);
define('T_DB_TABLE_PREFIX','nwp');

if ( strstr ( $_SERVER['HTTP_HOST'], 'localhost' ) || strstr ( $_SERVER['HTTP_HOST'], '10.0.1' )  || strstr ( $_SERVER['HTTP_HOST'], 'local.natwalker.com' ) ) 
  { 
    if ( strstr ( $_SERVER['HTTP_HOST'], 'local.natwalker.com' ) ) {
      define('T_HOME_URL', '/');
      } else {
      define('T_HOME_URL', '/wip/nw2015/app/');
    }

    define('CONTENT_IMG_UPLOADS', true);

    define('DB_HOST','localhost');
    define('DB_USER','usr.nw.com.2015');
    define('DB_PASS','7WYUBLQjj9HezmSC');
    define('DB_NAME','natwalker.com-2015');
    define('DISABLE_FRONT_END',false);

    define('BATCH_ATTEMPT_LIMIT', 'all' );
    define('BATCH_RESIZE_LIMIT', '50' );//'all' );
    
    define ( 'DEV_ENVIRONMENT', true );
  }
else
  {
    //settings for arvixe
    //define('T_HOME_URL', 'http://oxen.arvixe.com/~dualism/natwalker.com-2015/app/');
    define('T_HOME_URL', '/');
    define('CONTENT_IMG_UPLOADS', true);

    define('DB_HOST','localhost');
    define('DB_NAME','dualism_natwalker_com_2015');
    define('DB_USER','dualism_nw2015');
    define('DB_PASS','9DxjTZnoj7kzvZ');
    define('DISABLE_FRONT_END',false);

    define('BATCH_RESIZE_LIMIT', 'all' );//'all' );

    define ( 'DEV_ENVIRONMENT', false );
}


define( '_VALID_MOS', 1 );

define ('WEBSITE_NAME', 'Natalie Walker, Photographer');

/*Batchc settings*/
define('CRON_BATCH_PASSWORD', 'JzGb3zHrU');

/*Image display settings*/
define('HD_UNIT', 1080);
define('ASSUMED_SCREEN_WIDTH', 1 * HD_UNIT);
define('MIN_HERO_WIDTH', 400);
define('HERO_ANIMATION_SPEED', 100);

define('GALLERY_IS_MULTI_PAGE', false);
//define('SCREEN_HERO_RATIOS', serialize( array ('800'=>'720','1080'=>'1024' ,'2060'=>'2048','4120'=>'4096' ) ) );
define('SCREEN_HERO_RATIOS', serialize( array ('800'=>'720','1080'=>'1024'  ) ) );
define('GALLERY_THUMB_WIDTH', 200 );
define('GALLERY_THUMB_HEIGHT', 120 );
//set width to false to resize by height
define('GALLERY_THUMB_RESIZE_WIDTH', 200 );
//set height to false to resize by width
define('GALLERY_THUMB_RESIZE_HEIGHT', -1 );

define('GALLERY_THUMB_MARGIN_RIGHT', 12 );
define('GALLERY_THUMB_MARGIN_BOTTOM', 12 );
//serializing this: double check exports
define('ALL_IMG_HEIGHTS', serialize( array ( GALLERY_THUMB_HEIGHT, '720','1024' ,'2048','4096' ) ) );

define('ENLARGE_SMALL_HEROS', false );

define('T_INC_DIR',T_ABS_PATH . '/includes');
define('T_CLASS_DIR',T_ABS_PATH . '/classes');
define('T_TEMPLATES_DIR',T_ABS_PATH . '/templates');

define('DEFAULT_CMS_PAGE', T_HOME_URL . 'cms/photos' );
define('IMG_UPLOAD_DIR', T_ABS_PATH . '/media/images/orig' );
define('REL_UPLOAD_DIR', T_HOME_URL . 'media/images/orig' );
define('REL_RESIZED_DIR', T_HOME_URL . 'media/images/resized' );
define('ABS_RESIZED_DIR', T_ABS_PATH . '/media/images/resized' );
define('HERO_COMPRESSION', 70 );
define('THUMB_COMPRESSION', 90 );
define('CMS_THUMB_HEIGHT', 60 );
define('CMS_THUMB_HEIGHT_HOVER', 120 );

define('IMG_MAX_SIZE', 8192 );//kb
define('IMG_ALLOWED_TYPES', 'image/jpeg:image/pjpeg' );
define('CMS_THUMB_HEIGHT', 200 );//kb

define( 'PRELOAD_FIRST_HERO', false );
define( 'GALLERY_HERO_WIDTH', 960 );
define( 'GALLERY_HERO_HEIGHT', 590 );
define( 'GALLERY_HERO_PADDING', 20 );
define( 'GALLERY_HERO_PADDING_PHONE', 10 );
define( 'GALLERY_HERO_PADDING_TABLET', 10 );
define( 'MAX_RETAINED_HEROS', 3 );
define( 'ASSUMED_SCREEN_HEIGHT', 1080 );

define( 'GALLERY_THUMB_HEIGHT', 120 );
define( 'GALLERY_THUMBS_PER_PAGE', 8 );

define( 'CONTACT_RECIPIENT', 'ben@dualism.com.au:info@natwalker.com' );
define( 'CONTACT_SUBJECT', 'Contact from the ' . WEBSITE_NAME . ' Website' );
define( 'ADMIN_EMAIL', 'info@natwalker.com' );

//site 
define('T_SITE_CODE','nw2014');
define('T_SITE_READABLE','Natalie Walker');
define('T_SITE_TITLE','Natalie Walker, Photographer');

define('_ADMIN_EMAIL','ben@dualism.com.au');


define('T_HASH_SALT', '&Xkso8');//72x11xlkrr2');

//database

if(extension_loaded('gd')) {
  //print_r(gd_info());
  define('GD_AVAILABLE', true);
} else  {
  define('GD_AVAILABLE', false);
}

if(extension_loaded('imagick')) {
  define('GD_AVAILABLE', false);
  //print_r($imagick->queryFormats());
} else {
  define('IMAGICK_AVAILABLE', false);
}


?>