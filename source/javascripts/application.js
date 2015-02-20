//= require layout.engine.min

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

if ( 'querySelector' in document && 'addEventListener' in window ) {

  var ready;
  ready = function() {

    var body = document.querySelector('body'),
        html = document.querySelector('html'),
        wrapper = document.getElementById("wrapper"),
        menuLink = document.querySelector('.resp-nav');

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

    animateClass = function() {
      if(hasClass(html, 'animate-out')) {
        removeClass(html, 'animate-out')
        addClass(html, 'animate-in')
      } else {
        removeClass(html, 'animate-in')
        addClass(html, 'animate-out')
      }
    }

    fixIE9 = function() {
      function reSize() {
        [].forEach.call(document.querySelectorAll('.client'), function(el) {
          var itemHeight = body.offsetHeight / 3;
          el.style.height = itemHeight + 'px';
        })
      }
      reSize();
      window.addEventListener('resize', reSize, false);
    }

    tonedenInit = function () {
      if(hasClass(wrapper, 'page-music')) {
        // Now we are ready to
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
    }

    if(hasClass(html, 'resp')) {
      removeClass(html, 'resp');
    }

    tonedenInit();
    workNav();

    if(hasClass(html, 'ie9')) {
      fixIE9();
    }

  }; 

  loadToneDen = function() {
    var tdScript = document.createElement("script");
    tdScript.type = "text/javascript";
    tdScript.async = true;
    tdScript.src = "//sd.toneden.io/production/toneden.loader.js"
    var entry = document.getElementsByTagName("script")[0];
    entry.parentNode.insertBefore(tdScript, entry);
    var tdStyle = document.createElement("style");
    tdStyle.type = "text/css";
    cssStyles = '.td.player.light,.td.player.light div,.td.player.light ul,.td.player.light .follow-link{background-color: transparent !important;border-color: #034A8E !important;color: #fff;}.td.player.light .slider>.track{background-color: #034A8E !important;}.td.player.light .slider>.highlight-track {border: 0 !important;background-color: #fff !important;}.td.player.light .info a, .td.player.light .mini-song-info a,.td.player.light .follow-link {color: #fff !important;}.td.player.light .waveform path {stroke: #fff !important;}.td.player.light .buy-link:hover, .td.player.light .follow-link:hover {background-color: transparent !important;color: #034A8E !important;}.td.player.light .slider>.dragger {background-color: #fff !important;}.td.player.solo .info-solo,.td.player.solo .scrubber {border-left: 0 !important; }a.tdbutton.expand.buy-link {color: #fff !important;}'
    if (tdStyle.styleSheet){
      tdStyle.styleSheet.cssText = cssStyles;
    } else {
      tdStyle.appendChild(document.createTextNode(cssStyles));
    }
    document.getElementsByTagName('head')[0].appendChild(tdStyle);
  }

  document.addEventListener("DOMContentLoaded", function() {
    loadToneDen();
    ready();
    window.addEventListener("click", respNav, false);
  }, false);

  document.addEventListener("page:fetch", function() {
    animateClass();
  }, false);

  document.addEventListener("page:load", function() {
    animateClass();
    ready();
    window.scrollTo(0,0);
  }, false);

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