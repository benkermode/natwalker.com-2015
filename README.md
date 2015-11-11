
# natwalker.com-2015
Custom AngularJS and OO PHP CMS.
Photography Portfolio Site, originally built in raw OO JS. The client requested a rebuild, to enable thumbnail images of fixed width, varying height. Of course, images are much easier to lay out dynamically when they're fixed height, but the requested fixed width looks much cooler. The site should obviously be responsive.

## /angular-modules
/angular-modules contains all AngularJS code. The code base designed to be very modular, though it's gulped, concatenated and minified on production.

### CheckThumbColumns service
The CheckThumbColumns service (/angular-modules/folio/js/services.js) is responsible for setting up several MediaWatch functions, which can re-tile the images based on changing screen-width. MediaWatch eliminates the need for an expensive, recurring window.onResize. CheckThumbColumns is reponsible for calling the tiling function, but that function can be called from a directive, or a media query change, and it also requires that: a. data is loaded, b. the template directive is loaded and the DOM is ready. CheckThumbColumns handles all of this logic internally, without using ui-router resolve, which makes it harder to display a loading cue if the user requests a folio page directly in their browser (or does a reload).

### ImageFactory service
Keeping with the modular intentions, the ImageFactory service (/angular-modules/folio/js/services.js) handles all logic relating to images. It will take obscure image data from the DB and reformat it into objects and properties that are easily understandable outside of that service. It tiles images dynamically based on the current data set and the screen resolution (using CSS 3 animations and absolute positioning to reduce reflow). It will only request hero images from the server when the user requests them. Array references mean that any previously requested heros are saved for the session, and quickly reloaded when required (via browser cache). 

##/classes
The PHP classes are in /classes. This is custom PHP CMS that was built a while back. It uses jQuery UI to let the admin drag images around to re-order them. Admin can also upload singles copies of full resolution images, and a CRON job will later go through and batch all outsanding images according to the current set of image sizes saved in the DB.
