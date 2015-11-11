'use strict';

angular.module ( 'natApp.services', [])

.factory('GlobalData', function () {
    return {};
})

.factory ( 'CheckMenuHidden',  [ 'GlobalData', '$window', 'moveElement', function ( GlobalData, $window, moveElement ) {
  return {
    init: function () {
      var menuHidden = $window.matchMedia( "(max-width: 767px)" );
      var checkMenuHidden = function ( mediaQueryList ) {
        GlobalData.menuHidden = mediaQueryList.matches;
        //if menu is hidden, move Nav to hidden position, otherwise TileThumbs will do it 
        if ( GlobalData.menuHidden ) {
          moveElement.moveEl( 'hideMenu' );
        }
      }
      menuHidden.addListener( checkMenuHidden );
      checkMenuHidden ( menuHidden );
    }
  }
}])

.factory ( 'moveElement', [ '$window','GlobalData','Settings', function ( $window, GlobalData, Settings ) {
  return {
    //we could wrap this in a timeout to avoid using window, but seems like overkill
    marginRight: Number ( Settings.galleryThumbMarginRight ),
    init: function ( element, attrs ) {
      //these only need to be performed once
      this.ownWidthEl = document.getElementById (  attrs [ 'ownWidth' ] );
      this.hitchToEl = document.getElementById (  attrs [ 'hitchTo' ] );
      this.element = element;
      this.moveEl ();
    },
    moveEl: function ( which, bool ) { 
      console.log ( 'moveEl()');
      //CheckMenuHidden will call hideMenu only once when the media query changes
      //setting to '' allows the css to take over the element, including browser transitions
      if  ( (this.hitchToEl == null ) || ( which == 'hideMenu') ) {
        this.element[0].style.left = '';
        //otherwise window resize is calling this, but only act if menu is not hidden
      } else if ( !GlobalData.menuHidden ) {
        var newLeft = this.hitchToEl.offsetLeft - ( this.ownWidthEl.clientWidth + this.marginRight );
        this.element[0].style.left = newLeft + 'px';
      }
      if ( this.element[0].style.opacity == 0 ) {
       this.element[0].style.opacity = '1';
      }
      //Else menu is hidden, no need to move on window resize
      }
      
  }
}])

;
