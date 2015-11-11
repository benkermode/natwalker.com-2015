'use strict';

angular.module ( 'natApp.controllers', [] )

  .controller ( 'ApplicationController', [ 'GlobalData','GetMedia', 'CheckThumbColumns', 'ImageFactory', '$scope', '$stateParams', 'Settings', '$rootScope', '$timeout',  function ( GlobalData, GetMedia, CheckThumbColumns, ImageFactory, $scope, $stateParams, Settings, $rootScope, $timeout ) {
    //console.log ( 'ApplicationController()' );

    //make the current ui-router state avail in all view scopes
    $rootScope.$on( '$stateChangeStart',  function( event, toState, toParams, fromState, fromParams ){ 
      //toState is the full state object, with url, templateUrl, name, controller
      $scope.currentState = toState;
      //toParams are the url parameters passed in the url
      $scope.folio = ( toParams.folio ) ? toParams.folio : false;
      //console.log ( 'chagne state to: '  + toState );
    });

    $scope.GlobalData = GlobalData;
    GlobalData.homeUrl = Settings.homeUrl;
    var data = { "action": 'getSiteData' };
    var url = '/ajax';
    GlobalData.siteDataLoaded = false;
    GetMedia.GetFromAjax( url, data )
      .then ( function ( response ) {
        console.log (  'data received'  );
        GlobalData.siteDataLoaded = true;
        //if this is a folio subsection being requested as first page
        if ( $scope.folio ) {
          //CheckThumbColumns will check internally that both data is loaded, and that the gileGallery directive is loaded
          CheckThumbColumns.init();
        }
        //console.log (  JSON.stringify ( response ));
        ImageFactory.constructImageData ( response.data.image_ids );
      $scope.sections = response.data.sections;
    });

  }]) // end Application Controller


  .controller ( 'ContactController', [ '$scope', '$http', '$httpParamSerializerJQLike', 'Settings', function ( $scope, $http, $httpParamSerializerJQLike, Settings ){
    console.log ('ContactController' );
    var ajaxUrl = '/ajax';
    $scope.emailSendError = false;
    $scope.emailSending = false;
    $scope.emailSuccess = false;
    $scope.contactFormSubmitted = false;
    $scope.submit = function () {
      $scope.contactFormSubmitted = true;
      //the model object: $scope.contact, that has the actual form values
      console.log ( 'contact submit: ' );
      if ( $scope.contactForm.$valid ) {
      console.log ( 'contact valid' );
        $scope.emailSending = true;
        $scope.contactFormSubmitted = false;
        $scope.contact.action = 'contactFormSubmit';
        //serialize data so that php can read as $_POST (not $params)
        var data = $httpParamSerializerJQLike( $scope.contact );
        $http({
          "url": ajaxUrl,
          "method": 'POST',
          "data": data, 
          "headers": { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then ( function  ( response ) { 
          //success handler
          $scope.emailSending = false;
          $scope.emailSendError = ( response.data.email_sent == true ) ? false : true;
          $scope.emailSuccess = ( response.data.email_sent == true ) ? true : false;
          //console.log ( 'response: ' + JSON.stringify ( response ) );
        }, function ( response ) {
          //error handler
          emailSendError = true;
          emailSending = false;
          //console.log ( 'error: ' + JSON.stringify ( response ) );
        });

      }
    }

  }])//end ContactController

;
