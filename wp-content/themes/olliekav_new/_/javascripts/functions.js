// remap jQuery to $
(function($){
  
  
})(window.jQuery);

var $masonry = jQuery('#latest_work');

// initialize masonry
$masonry.imagesLoaded(function(){
  
  var colWidth = $masonry.width() / 3;
  
  $masonry.masonry({
    singleMode: true,
    itemSelector: 'article',
    isResizable: false,
    columnWidth: colWidth
  });
});

$(window).resize(function(){
  
  $masonry.masonry({
    singleMode: true,
    isResizable: false,
    columnWidth: colWidth
  });
  
});

