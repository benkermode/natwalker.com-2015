
# natwalker.com-2015
Custom AngularJS and OO PHP CMS.
Photography Portfolio Site, originally built in raw OO JS. The client requested a rebuild, to enable thumbnail images of **fixed width and varying height**. Images are much easier to lay out dynamically when they have fixed height, but the requested **fixed width, varying height** looks much cooler. Additionally the site needs to be responsive, of course.

## /angular-modules
/angular-modules contains all AngularJS code. There is a clear file and directory structure for modules. On production, however, all these files are gulped, concatenated and minified to a single *bundle.js*.

### CheckThumbColumns service
The **CheckThumbColumns** Service ([services.js](angular-modules/folio/js/services.js)) is responsible for setting up several matchMedia functions, which can re-tile the photography images based on changing screen-width. MediaWatch eliminates the need for an expensive, recurring window.onResize. The media query events are the main reason to externalise the DOM Manipulation in a Service, not a Directive (though the Directive can and must call the Service).
This Service must handle various logic internally, including the fact that the load order of a ui-router template, and async data from the DB is unpredictable: it must handle all situations: the DOM and the data must be ready. 

### ImageFactory service
Keeping with the modular intentions of the projects, the **ImageFactory** Service ([services.js](angular-modules/folio/js/services.js)) handles all logic relating to images. **ImageFactory.constructImageData()** takes obscure image data from the DB and reformats it into objects and properties that are easily understandable outside of that service (in modular fashion). **ImageFactory.tileThumbs()** tiles images dynamically based on the current image data set and the screen resolution (using CSS 3 animations and absolute positioning to reduce reflow). **ImageFactory.showHero()** will only request hero images from the server when the user requests them. Array references mean that any previously requested heros are saved across the entire session, and quickly reloaded (via browser cache) when the user switches to a different "folio" and switches back.

##/classes
All PHP classes are in /classes. This is custom PHP CMS that was built a while back. It uses jQuery UI to let the site Admin drag images around to re-order them. The Admin can also upload single copies of full resolution images, and a CRON job will later go through and batch all outsanding images according to the current set of image sizes saved in the DB. All the data for every image, at every size, is sent to the Angular app via trusty async $http, in the format of a colon-separted, comma-separated string, which is reformatted by **ImageFactory.constructImageData()** above.
