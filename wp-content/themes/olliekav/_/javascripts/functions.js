// remap jQuery to $
(function($){
	
	var $container = $('#latest_work');
	var colWidth = $container.width() / 3;

	$container.imagesLoaded( function(){
	  $container.masonry({
	    itemSelector : '.post',
			isFitWidth: true,
			isResizable: true,
			gutterWidth: 5
	  });
	});
  
})(window.jQuery);

/*var $masonry = jQuery('#latest_work');

// initialize masonry
$masonry.imagesLoaded(function(){
  
  var colWidth = $masonry.width() / 3;

	$(window).resize(function(){

	  $masonry.masonry({
	    itemSelector: 'article',
	    isResizable: false
	  });

	}).resize();

});
*/

/*$(window).resize(function(){
  
  $masonry.masonry({
    singleMode: true,
    isResizable: false,
    columnWidth: colWidth
  });
  
});*/

