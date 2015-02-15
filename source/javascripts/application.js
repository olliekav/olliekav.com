//= require turbolinks

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

Turbolinks.enableProgressBar();
Turbolinks.enableTransitionCache();

if ( 'querySelector' in document && 'addEventListener' in window ) {

  var body = document.querySelector('body'),
      html = document.querySelector('html');
      // height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
      // width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
      // top = document.documentElement.scrollTop || document.body.scrollTop;

  // Add scrolling class to body
  function scrolling() {
    var fired = false;
    window.addEventListener('scroll', function() {
      var body = document.querySelector('body');
      if(window.pageYOffset >= 50 && fired === false) {
        addClass(body, 'scrolling');
        //fired = true;
      }
      else {
        removeClass(body, 'scrolling');
      }
    });
  }

  // Responsive nav menu
  function responsiveNav() {
    var menuLink = document.querySelector('.resp-nav');
    menuLink.addEventListener("click", function(event){ 
      console.log('clicked');
      if (hasClass(html, 'resp')) {
        removeClass(html, 'resp')
        removeClass(menuLink, 'open')
      }
      else {
        addClass(html, 'resp')
        addClass(menuLink, 'open')
      }
      event.preventDefault();
    });
  }

  // Make SVG link play nice with turbolinks
  function svglink() {
    var svg = document.querySelector('.logo');
    svg.addEventListener("click", function(event){ 
      Turbolinks.visit('/');
      event.preventDefault();
    });
  }

  // ie9 fixes for flexbox
  function fixie9() {
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

  // Music player
  function tonedenInit() {
    console.log("I'm running")
    ToneDenReady = window.ToneDenReady || [];
    var configOne = {
      dom: "#player-1",
      urls: [
        "https://soundcloud.com/olliekav/ollie-k-dnb-mix-270513"
      ]
    }
    var configTwo = {
      dom: "#player-2",
      urls: [
        "https://soundcloud.com/olliekav/ollie-k-drum-bass-mix-2000"
      ]
    }
    var configThree = {
      dom: "#player-3",
      urls: [
        "https://soundcloud.com/olliekav/drum-and-bass-mix-2005"
      ]
    }
    var configFour = {
      dom: "#player-4",
      urls: [
        "https://soundcloud.com/olliekav/olliek-mixjune04"
      ]
    }
    var configFive = {
      dom: "#player-5",
      urls: [
        "https://soundcloud.com/olliekav/ollie-k-house-techno-mix-21-02"
      ]
    }
    var configSix = {
      dom: "#player-6",
      urls: [
        "https://soundcloud.com/olliekav/ollie-k-house-techno-mix-19-05"
      ]
    }
    var configSeven = {
      dom: "#player-7",
      urls: [
        "https://soundcloud.com/olliekav/house-techno-mix-12-12-08"
      ]
    }

    // ToneDenReady.push(function() {
    //   ToneDen.player.create(configOne);
    //   ToneDen.player.create(configTwo);
    //   ToneDen.player.create(configThree);
    //   ToneDen.player.create(configFour);
    //   ToneDen.player.create(configFive);
    //   ToneDen.player.create(configSix);
    //   ToneDen.player.create(configSeven);
    // }); 

    if(typeof ToneDen != 'undefined'){
      ToneDen.player.create(configOne);
      ToneDen.player.create(configTwo);
      ToneDen.player.create(configThree);
      ToneDen.player.create(configFour);
      ToneDen.player.create(configFive);
      ToneDen.player.create(configSix);
      ToneDen.player.create(configSeven);
    } else {
      ToneDenReady.push(function() {
        ToneDen.player.create(configOne);
        ToneDen.player.create(configTwo);
        ToneDen.player.create(configThree);
        ToneDen.player.create(configFour);
        ToneDen.player.create(configFive);
        ToneDen.player.create(configSix);
        ToneDen.player.create(configSeven);
      }); 
    }
  }

  document.addEventListener("DOMContentLoaded", function() {
    scrolling();
    responsiveNav();
    fixie9();
    svglink();
    tonedenInit();
  });

  document.addEventListener("page:fetch", function() {
    removeClass(html, "resp");
    removeClass(html, "animate-in");
    addClass(html, "animate-out");
    scrolling();
    responsiveNav();
    svglink();
    fixie9();
    window.scrollTo(0,0);
  });

  document.addEventListener("page:load", function() {
    removeClass(html, "animate-out");
    addClass(html, "animate-in");
    tonedenInit();
  });

}


// if (document.all && document.querySelector && !document.addEventListener) {

//   var index;
//   var svgs = document.querySelectorAll('.svg');
//   for (index = 0; index < svgs.length; ++index) {
//     img = svgs[index];
//     var title = img.getAttribute('alt');
//     img.style.display = 'none';
//   }

// }