/*filters.js*/
angular.module ( 'natApp.folio.filters', [])

.filter ( 'SetHeroSrc', [ 'GlobalData', function ( GlobalData ) { 
  return function ( index ){
    var id = GlobalData.imageIds [GlobalData.currentFolio] [ index ].id;
    var src = GlobalData.Settings.resizedUrl + '/' + GlobalData.Settings.heroDirectory + '/' + id + '.jpg';
    return src;
  }
}])

;
