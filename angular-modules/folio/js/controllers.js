/*controllers.js*/
angular.module ( 'natApp.folio.controllers', [ ])

  .controller ( 'FolioController', [ '$scope', '$stateParams', '$location', 'ImageFactory', function ( $scope, $stateParams, $location, ImageFactory ) {
    //console.log ( 'FolioController(): '  );
    $scope.ImageFactory = ImageFactory;

    //specify the subsect of images according to the url parameter
    ImageFactory.setCurrentFolio ( $stateParams.folio );

    $scope.HerosVisible = false;
    //any writing to a service, go through the scope, otherwise view can read from the service
    var basePath = $location.url();
    $scope.showHero = function ( doShow, index, heroid ) {
      ImageFactory.showHero ( doShow, index, heroid );
      $scope.HerosVisible = doShow;
      $scope.curHeroId = ImageFactory.curHeroId;
    }


  }])//end FolioCtrl  

;


