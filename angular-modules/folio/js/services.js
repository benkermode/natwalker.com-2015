/*services.js*/
/*Syntax: module.service( 'serviceName', function ); 
  Result: When declaring serviceName as an injectable argument you will be provided with an instance of the function. In other words new FunctionYouPassedToService().*/
angular.module ( 'natApp.folio.services', [])

  .factory('GlobalData', function () {
    return {};
  })

  .service ( 'GetMedia', [ '$http', function ( $http ) {
    return {
      GetFromAjax: function ( url, data) {
        return $http.post ( url, data );
      }
    }
  }])

//keep complex logic contained in the imageFactory: heavy lifting here. ImageData outside the factory should be super simple to read
  .factory ( 'ImageFactory', [ 'Settings','GlobalData', 'moveElement', function ( Settings, GlobalData, moveElement ) {
    //code outside the return object will run before any controller: init variables here
    var _thumbDirectory = ( Settings.galleryThumbResizeHeight == '-1' ) ? Settings.galleryThumbResizeWidth + 'w' : Settings.galleryThumbResizeHeight + 'h';

    //set Factory private variables outside of the return object
    var _currentFolio = false;
    var _imageIds = {};

    function    _getNextOrPrevImageId( index, nextOrPrev, arrayLength ) {
      if ( nextOrPrev == 'next' ) {
        index = ( index == ( arrayLength - 1 ) ) ? 0 : index + 1;
      } else if ( nextOrPrev == 'prev' ) {
        index = ( index == 0 ) ? ( arrayLength - 1 ) : index - 1;
      }
      return index;
    } //end getNextOrPrevImageId

    //set public methods and properties on the return object
    //---RETURN OBJECT---
    return {

      images: [],
      numThumbCols: 4,
      GalleryThumbContainer: undefined,
      curHeroIndex: 0,
      curHeroId: -1,
      constructImageData:  function ( data ) {
        //console.log ( JSON.stringify ( data ) );
        angular.forEach ( data, function ( value, key ) {
          var imageData = String ( value );
          var imageDataArray = imageData.split ( ',' );
          var id = parseInt ( imageDataArray [ 0 ] );
          var hasSizesArray = imageDataArray [ 1 ].split ( '.' );
          var currentCategory = imageDataArray [ 2 ];
          //console.log ( 'currentCategory: ' + currentCategory );
          //data for each image in the db has a category, eg 'corporate', 'events', 'fashion'
          //create the sub array for the category if it doesn't exist
          if ( !_imageIds [ currentCategory ] ) {
            _imageIds [ currentCategory ] = [];
          }
          //sizesObject is a temporary object for all sizes associated with 1 hero image
          var sizesObject = {};

          //image size info arrives from the db as hasSizesArray in obscure format:  200:133:w,1080:720:h,1536:1024:h
          //format the internal JS objects into something much more readable
          angular.forEach ( hasSizesArray, function ( v, k ) {
            //we want to search by a width or height, not iterate over everything to find the right dimensions
            //eg to look dimensions of a thumb 200 wide for id of 36, we want to call, for width: 36.hasSizes.200w[0], and for height 36.hasSizes.200w[ 1 ]
            hasSizesArray [ k ] = v.split ( ':' );
            //definingSizeProperty is the dimension in pixels, concatenated with 'w' or 'h', definingin which dimension, eg 200w
            var definingSizeProperty = ( hasSizesArray [ k ] [ 2 ] == 'w' ) ? hasSizesArray [ k ] [ 0 ] + 'w' : hasSizesArray [ k ] [ 1 ] + 'h';
            // we use definingSizeProperty as a property so we can call on a set of HERO images appropriate for the client screen size
            sizesObject [ definingSizeProperty ] = {};
            //use property names that are easily understand outside of this service, eg 'width', 'height'
            sizesObject [ definingSizeProperty ].width = hasSizesArray [ k ] [ 0 ];
            sizesObject [ definingSizeProperty ].height = hasSizesArray [ k ] [ 1 ];
            sizesObject [ definingSizeProperty ].definingDimension = hasSizesArray [ k ] [ 2 ];
            sizesObject [ definingSizeProperty ].definingDimension = hasSizesArray [ k ] [ 2 ];
            //!! we DON'T set the HERO src here: for media management purposes: do that on showHero
          });

          var aspect = ( sizesObject [ _thumbDirectory ] [1] > sizesObject [ _thumbDirectory ] [0] ) ? 'portrait' : 'landscape';
          var thumb = sizesObject[ _thumbDirectory ];
          thumb.src = Settings.resizedUrl + '/' + _thumbDirectory + '/' + id + '.jpg';
          var hero = sizesObject[ Settings.heroDirectory ];
          //save thumb and hero information in an easily readable object 
          var imgData = {
            "id" : id,
            "aspect" : aspect,
            "thumb": thumb,
            "hero": hero
          };
          //add the image to the end of the appropriate category array: the images don't have to be grouped in category order in database: they will still be saved in the correct category here
          _imageIds [ currentCategory ].push ( imgData );

        });//End foreach imageIds

        //if _currentFolio is set, it was set by a folio page loading: we need to set current folio with real data
        if ( _currentFolio ) {
          this.setCurrentFolio ( _currentFolio );
        }
      }, //end constructImageData

      setCurrentFolio: function ( which ) {
        _currentFolio = which;
        //console.log ( 'setCurrentFolio: ' + _currentFolio );
        //make the current subset of all the image data appear to be one simple persistent data set outside this service
        this.images = _imageIds [ _currentFolio ];
        this.curHeroIndex = 0;
        //tile thumbs must be initiated from the directive, as the template is loaded dynamically
        //this.tileThumbs();
      },

      tileThumbs: function () {
        console.log ( 'ImageFactory.tileThumbs()' );
        GlobalData.currentThumbCols = this.numThumbCols;
        var element = this.GalleryThumbContainer [0];
        var curColIndex = 0;
        var currentColumnTotalHeights = [];
        for ( var i = 0; i < this.numThumbCols; i ++) {
          currentColumnTotalHeights [ i ] = 0;
        }
        Array.min = function ( array ) {
          return Math.min.apply ( Math, array );
        }
        Array.max = function ( array ) {
          return Math.max.apply ( Math, array );
        }
        //need to pass the context for this as the final parameter to angular.forEach, which in this case, is this
        angular.forEach ( element.children, function ( domElement, key ) {
          //domElement is a DOM node, so you can mod css and access props with raw JS
          //if you need JQlite you can always use angular.element ( value ) later
          //find the value of the sho
          var shortestColumnValue = Array.min (currentColumnTotalHeights);
          //make the shortest column the current one
          var curColIndex = currentColumnTotalHeights.indexOf( shortestColumnValue );
          var left = curColIndex * ( Number ( Settings.galleryThumbResizeWidth ) + Number ( Settings.galleryThumbMarginRight ) );
          var top = shortestColumnValue;
          //add the known height of the image + margin to column height
          currentColumnTotalHeights [ curColIndex ] += Number ( this.images[key].thumb.height  ) + Number ( Settings.galleryThumbMarginBottom );
          domElement.style.left = ( left + 'px' );
          domElement.style.top = ( top + 'px' );

        }, this );//end foreach

        var tallestColumnValue = Array.max (currentColumnTotalHeights);
        element.style.height = tallestColumnValue - Settings.galleryThumbMarginBottom + 'px';
        var widthAllCols = this.numThumbCols * ( Number ( Settings.galleryThumbMarginRight ) + Number ( Settings.galleryThumbResizeWidth  ) ) - Settings.galleryThumbMarginRight;
        element.style.width =  Math.round ( widthAllCols ) + 'px';

        moveElement.moveEl ();
      }, //end tileThumbs()

      showHero: function ( doShow, index, heroid ) {
        //if heroid is set, use heroid, not index 
        if ( heroid  ) {
          //in case there's a problem with the id: eg that id no longer available
          index = 0;
          angular.forEach ( this.images, function ( v, k ) {
            if ( v.id == heroid ){
              index = k;
            }
          });
        }
        //the block used by the left and right arrows to switch hero left and right 
        if ( index == 'prev' || index == 'next' ) {
          index = _getNextOrPrevImageId ( this.curHeroIndex, index, this.images.length );
        }

        if ( doShow ) {
          this.curHeroIndex = index;
          this.curHeroId = this.images [ this.curHeroIndex ].id;
          this.prevHeroIndex = _getNextOrPrevImageId ( this.curHeroIndex, 'prev', this.images.length );
          this.nextHeroIndex = _getNextOrPrevImageId ( this.curHeroIndex, 'next', this.images.length );

          //MEDIA MANAGEMENT
          //when setCurrentFolio() switches the _currentFolio, all the img tags for heros in the current folio will be built by ng-repeat, with empty src: src="#". So no http request
          //when the user requests a hero, the current, previous and next hero will be requsted by setting the appropriate array item
          //so next and previous heros are preloaded, to create a seamless user experience
          //the requested src paths will be saved in the array, even if the user switches to another folio: Because each folio is a sub-array
          //when the user comes back to a previously viewed folio, all previously loaded heros will have their src assigned instantly
          //thus all the heros already cached by the browser in the session are loaded immediately
          if ( !this.images [ this.curHeroIndex ].hero.src ) {
            var src = Settings.resizedUrl + '/' + Settings.heroDirectory + '/' + this.images[ this.curHeroIndex ].id + '.jpg';
            this.images [ this.curHeroIndex ].hero.src = src;
          }
          if ( !this.images [ this.prevHeroIndex ].hero.src ) {
            var src = Settings.resizedUrl + '/' + Settings.heroDirectory + '/' + this.images[ this.prevHeroIndex ].id + '.jpg';
            this.images [ this.prevHeroIndex ].hero.src = src;
          }
          if ( !this.images [ this.nextHeroIndex ].hero.src ) {
            var src = Settings.resizedUrl + '/' + Settings.heroDirectory + '/' + this.images[ this.nextHeroIndex ].id + '.jpg';
            this.images [ this.nextHeroIndex ].hero.src = src;
          }
          // angular.forEach ( GlobalData.heroSrcPaths, function ( v, k ) {
          //   console.log ( k + ': ' + v );
          // });
        } 

      }//endshow hero

    };//end return object

  }])//end ImageFactory

//this service should init just once the PAGE loads, to set up the mediawatchers
//but it will be called by the tile-gallery directive by each different folio section each time the TEMPLATE loads
  .factory ( 'CheckThumbColumns', [ '$window', '$timeout', '$injector', 'ImageFactory', 'GlobalData', function ( $window, $timeout, $injector, ImageFactory, GlobalData ) {
    var thisHasInit = false;

    return {
      init: function () {
        //only set up the watchers once
        console.log ( 'CheckThumbColumns.init()' );
        //tileGallery directive will call this service when the folio template is loaded, and specify it's DOM element on ImageFactory.GalleryThumbContaine
        //ApplicationController will also call this service when data is loaded
        //both must be loaded before we should execute this block
        //use timeout (0) to call tileThumbs to give the DOM time to actually be ready to be manipulated
        if ( GlobalData.siteDataLoaded && ImageFactory.GalleryThumbContainer ) {

          if ( thisHasInit ) {
            //if watchers already set up, still need to tileThumbs
            $timeout ( function () {
              ImageFactory.tileThumbs ();
            }, 0 );
          } else {
            //console.log ( 'CheckThumbColumns.init() - thisHasInit: ' + thisHasInit );
            //they need to have one break point each
            var fourCol = $window.matchMedia("(min-width: 1271px)");
            var threeCol = $window.matchMedia("(min-width: 950px) and (max-width: 1270px)");
            var twoCol = $window.matchMedia("(min-width: 0px) and (max-width: 949px)");
            var handleMediaChange = function ( mediaQueryList ) {
              //by testing only for a match, we fire this only once per change
              //ImageFactory.numThumbCols configures itself to 4 cols by default ( will tile 4 columns on browsers not supporting matchMedia)
              //** so MEDIA QUERIES, via JS, are calling the re-tiling of images when screen size changes, not window.onResize 
              if ( mediaQueryList.matches ) {
                if ( mediaQueryList.media.indexOf ( '949px' ) > -1 ) {
                  ImageFactory.numThumbCols = 2;
                } else if ( mediaQueryList.media.indexOf ( '950px' ) > -1 ) {
                  ImageFactory.numThumbCols = 3;
                } else if ( mediaQueryList.media.indexOf ( '1271px' ) > -1 ) {
                  ImageFactory.numThumbCols = 4;
                }
                $timeout ( function () {
                  ImageFactory.tileThumbs ();
                }, 0 );
              }
            }
            fourCol.addListener( handleMediaChange );
            threeCol.addListener( handleMediaChange );
            twoCol.addListener( handleMediaChange );
            handleMediaChange(twoCol);
            handleMediaChange(fourCol);
            handleMediaChange(threeCol);
            thisHasInit = true;
          }

        }

      }
    }
  }])

;
