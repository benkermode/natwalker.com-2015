<?
class galleryDisplay {
  public $hero;
  public $herfooId;
  public $thumbs = array();
  public $page;
  public $totalPages;
  public $s;
  public $images;
  public $imageIds = array();
  public $numTotalImages;
  public $galleryRow;
  public $lowerLimitInd;
  public $switch_link;
  public $heroRow;
  public $galleryIsMultiPage;
  public $screenHeroRatios;
  public $targetHeroHeight;

  public function __construct ( ) {
    $this->s = sectionsOb::getInstance()->section;
    $this->galleryIsMultiPage = GALLERY_IS_MULTI_PAGE;
  }

  public function showGallery()
  {
    //          genFuncs::getInstance()->spit ( $this->imageIds );
    //    $subsectVerbose = genFuncs::getInstance()->makeurl ( $this->s [ 'subsect' ], false ); 
    $this->galleryRow = tdb::getInstance()->getRow ( 'sections', 'section_name', $this->s [ 'subsect' ] [ 'section_name' ] );

    $this->images = tdb::getInstance()->getAll ( 'images', 'section_id', $this->galleryRow [ 'section_id' ], 'order_num', 'asc', 0, 9999, 'active_status', '1' );
    array_pop ( $this->images );
    foreach ( $this->images as $k=>$v )
      {
        $this->imageIds [] = array ( $v [ 'image_id'], $v ['image_orig_width'], $v ['image_orig_height'], $v [ 'image_has_sizes'] );
      }

    $this->numTotalImages = sizeOf ( $this->imageIds );//tdb::getInstance()->getRows ( 'images', 'section_id', $this->galleryRow [ 'section_id' ] );

    //    genFuncs::getInstance()->spit ( 'imageIds: ' . $this->imageIds );
    //    $this->totalPages = ceil ( $this->numTotalImages  / GALLERY_THUMBS_PER_PAGE );

    $this->heroId = -1;
    if ( $this->s [ '2' ] > 0 )
      {
        $this->heroId = $this->s [ '2' ];
      }
    else
      {
        $this->heroId = $this->images [ 0 ][ 'image_id' ];
      }
    $this->switch_link = '<a href="' . DEFAULT_CMS_PAGE . '/' . $this->s [ 'subsect' ] [ 'section_name' ] . '/' . $this->heroId . '">Edit Site &gt;</a>';


    $ratios = unserialize ( SCREEN_HERO_RATIOS );
    //mark //set assumed desiredheight for js too
    //genFuncs::getInstance()->spit ( 'size of ratios: ' . sizeOf($ratios) );
    $i = 0;

    $targetScreenDimension = ( $_SESSION['screen']['targetScreenDimension'] ) ? $_SESSION['screen']['targetScreenDimension'] : ASSUMED_SCREEN_HEIGHT;
    foreach ( $ratios as $k => $v ) 
      {
        $this->screenHeroRatios [ $i ] = array();
        $this->screenHeroRatios [ $i ] [ 0 ]  = $k;
        $this->screenHeroRatios [ $i ] [ 1 ]  = (int)$v;
        $i++;
        if ( $k >= $targetScreenDimension ) {
          $desired_height = $v;
          break;
        }
      }
    //allow for no match
    $this->targetHeroHeight = $desired_height;

    //this is only for server loaded heros: not used currently
    //$this->hero = img::getInstance()->getImage ( $this->heroId, false, $desired_height, false,  'array' );

  }

}
?>