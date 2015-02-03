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
      html = document.querySelector('html'),
      height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
      width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
      menuLink = document.getElementById('resp-nav'),
      top = document.documentElement.scrollTop || document.body.scrollTop;

  // Add scrolling class to body
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

  // Responsive nav menu
  menuLink.addEventListener("click", function(event){ 
    if (hasClass(body, 'resp')) {
      removeClass(body, 'resp')
      removeClass(menuLink, 'open')
    }
    else {
      addClass(body, 'resp')
      addClass(menuLink, 'open')
    }
    event.preventDefault();
  });

  // ie9 fixes for flexbox
  if(hasClass(html, 'ie9')) {
    function reSize() {
      [].forEach.call(document.querySelectorAll('.client'), function(el) {
        var itemHeight = body.offsetHeight / 3;
        el.style.height = itemHeight + 'px';
      })
    }
    reSize();
    window.addEventListener('resize', reSize, false);
  }

}