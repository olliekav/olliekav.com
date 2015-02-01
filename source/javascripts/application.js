var hasClass = function (elem, className) {
  return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
}

var addClass = function (elem, className) {
  if (!hasClass(elem, className)) {
    elem.className += ' ' + className;
  }
}

var removeClass = function (elem, className) {
  var newClass = ' ' + elem.className.replace( /[\t\r\n]/g, ' ') + ' ';
  if (hasClass(elem, className)) {
    while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
      newClass = newClass.replace(' ' + className + ' ', ' ');
    }
    elem.className = newClass.replace(/^\s+|\s+$/g, '');
  }
}

if ( 'querySelector' in document && 'addEventListener' in window ) {

  var body = document.querySelector('body'),
      width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
      menuLink = document.getElementById('resp-nav'),
      top = document.documentElement.scrollTop || document.body.scrollTop;

  console.log(menuLink)

  if(width > 320) {
    var fired = false;
    window.addEventListener('scroll', function() {
      if(window.pageYOffset >= 100 && fired === false) {
        addClass(body, 'scrolling');
        //addClass('#logo', 'to-top');
        //fired = true;
      }
      else {
        removeClass(body, 'scrolling');
        //removeClass('#logo', 'to-top');
      }
    });
  }

  menuLink.addEventListener("click", function(event){ 
    console.log('clicked');
    //var header = document.getElementById('#header');
    if (hasClass(body, 'resp')) {
      removeClass(body, 'resp')
    }
    else {
      addClass(body, 'resp')
    }
    event.preventDefault();
  }, false);

}