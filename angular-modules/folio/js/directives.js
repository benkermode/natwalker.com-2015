/*directives.js*/
angular.module ( 'natApp.folio.directives', [])

  .directive ( 'tileGallery', [ '$timeout', 'GlobalData', 'CheckThumbColumns', 'ImageFactory', function ( $timeout, GlobalData, CheckThumbColumns, ImageFactory ) {
    return {
      restrict: 'A',
      link: function ( scope, element, attrs ) {
        console.log ( 'tileGallery directive from folio templte' );
        //each new folio section, calls the directive again, but we only want to CheckThumbColumns.init once
        ImageFactory.GalleryThumbContainer = element;
        //ImageFactory needs to know the above DOM element, but when it is defined, it's also an external indicator that the DOM is ready for tileThumbs to be called

        //the checking to make sure data is loaded, and that this directive is loaded, is done internally at CheckThumbColumns
        CheckThumbColumns.init();
      }
    }
  }])

;
