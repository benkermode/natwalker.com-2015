'use strict';

angular.module ( 'natApp.directives', [])

  .directive ( 'navContainerPosition', [ '$window', '$timeout', 'moveElement', 'CheckMenuHidden', function ( $window, $timeout, moveElement,  CheckMenuHidden ) {
    return {
      restrict: 'A',
      link: function ( scope, element, attrs  ) {
        //console.log ( 'navContainerPosition directive attrs[ hitchTo ]: ' + attrs[ 'hitchTo']);
        //dealing with positioning the nav
        moveElement.init ( element, attrs );
        CheckMenuHidden.init();
        //window resize event
        angular.element($window).bind('resize', function() {
          moveElement.moveEl();
        })

        //dealing with allowing the menu to be toggled
        //should be deactivated while animation is happening
        scope.canToggleMenu = true;
        scope.showMenu = false;
        scope.toggleMenu = function ( bool ) {
          if ( scope.canToggleMenu ) {
            scope.canToggleMenu = false;
            scope.showMenu = bool;
            $timeout ( function() {
              scope.canToggleMenu = true;
            }, 10 );
          }
        }
      }
    }
  }])

//.directive ( 'moveNav', [ 'moveElement', function ( moveElement ) {
  .directive ( 'moveNav', [  '$timeout', 'moveElement', function ( $timeout, moveElement ) {
    return {
      restrict: 'A',
      link: function ( scope, element, attrs ){
        var wait_time = ( attrs [ 'moveNav' ] == 'wait' ) ? 50 : 0;
        $timeout ( function() {
          moveElement.moveEl();
        }, wait_time );
      }
    }
  }])

  .directive ( 'domLoadedAlert', [ function () {
    return {
      restrict: 'A',
      link: function( scope, element, attrs ) {
        console.log ( 'DOM element loaded: ' + JSON.stringify ( attrs.id ));
      }
    }
  }])

;
