'use strict';
/*app.js*/
angular.module ( 'natApp', [ 'ui.router', 'natApp.controllers', 'natApp.services', 'natApp.directives', 'natApp.filters', 'homeModule', 'folioModule' ])

   .constant( 'Settings', window.NatWalkerAppSettings )

  .config ( function ( $stateProvider, $urlRouterProvider, $locationProvider, Settings  ) {

    $stateProvider

      .state ( 'home', {
        'url': '',
        controller: 'HomeController',
        templateUrl: '/templates/home.html'
      } )

      .state ( 'folio', {
        url: '/folio',
        templateUrl: '/templates/home.html'
      } )

      .state ( 'folios', {
        url: '/folio/:folio',
        templateUrl: '/templates/folio.subsections.html',
        controller: 'FolioController'
      } )

      .state ( 'reviews', {
        'url': '/reviews',
        controller: '',
        templateUrl: '/templates/reviews.html'
      } )

      .state ( 'about', {
        'url': '/about',
        controller: '',
        templateUrl: '/templates/about.html'
      } )

      .state ( 'prices', {
        'url': '/prices',
        controller: '',
        templateUrl: '/templates/prices.html'
      } )

      .state ( 'contact', {
        'url': '/contact',
        controller: 'ContactController',
        templateUrl: '/templates/contact.html'
      } )


    ;

    $locationProvider.html5Mode ( false );

    $urlRouterProvider.otherwise( '' ); // when no route match found redirect to home

  })

;


