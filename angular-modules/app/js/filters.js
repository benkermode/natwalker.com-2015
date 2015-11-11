'use strict';

angular.module ( 'natApp.filters', [] )

  .filter ( 'navfilter', [   function () { 
    return function ( text ){
      text = text.replace(/_/g , ' ').toUpperCase();
      return ( text );
    }
  }])

;
