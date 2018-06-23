//= require layout.engine.min
//= require turbolinks

if ( 'querySelector' in document && 'addEventListener' in window ) {

  var hasClass = function (el, cls) {
    return el.className && new RegExp("(\\s|^)" + cls + "(\\s|$)").test(el.className);
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

  var ready;
  ready = function() {

    body = document.querySelector('body'),
    html = document.querySelector('html'),
    wrapper = document.getElementById("wrapper"),
    menuLink = document.querySelector('.resp-nav');

    if(hasClass(html, 'resp')) {
      removeClass(html, 'resp');
    }

    workNav();

  }; 

  workNav = function() {
    var fired = false;
    window.addEventListener('scroll', function() {
      if(window.pageYOffset >= 50 && fired === false) {
        addClass(body, 'scrolling');
        //fired = true;
      }
      else {
        removeClass(body, 'scrolling');
      }
    });
  }

  animateClass = function() {
    if(hasClass(body, 'animate-out')) {
      removeClass(body, 'animate-out')
      addClass(body, 'animate-in')
    } else {
      removeClass(body, 'animate-in')
      addClass(body, 'animate-out')
    }
  }

  respNav = function(e) {
    if(e.target && e.target.className === "resp-nav") {
      e.preventDefault();
      if(hasClass(html, 'resp')) {
        removeClass(html, 'resp')
      } else {
        addClass(html, 'resp')
      }
    }
  }

  document.addEventListener("turbolinks:load", function() {
    ready();
    window.addEventListener("click", respNav);
    window.scrollTo(0,0);
  }, false);

}