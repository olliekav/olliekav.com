## HTML5 Wordpress Theme with Compass and Sass

This is a Compass / Sass adaptation of the HTML5 Wordpress Theme which is a blank theme based on the [HTML5 Reset templates](https://github.com/murtaugh/HTML5-Reset). It's a great empty slate upon which to build your own HTML5-based Wordpress themes with the benefits of Compass and Sass.

## HTML5 Wordpress Theme Summary:

### hNews

In addition to all the standard Wordpress elements and classes, we have added the code required so that the single post template conforms with the [hNews microformat](http://microformats.org/wiki/hnews).

### HTML5 Reset brings to the table:

1. A style sheet designed to strip initial files from browsers, meaning you start off with a blank slate.
2. Easy to customize -- remove whatever you don't need, keep what you do.
3. Analytics and jQuery snippets in place
4. Meta tags ready for population
5. Empty mobile and print style sheets, including blocks for device orientation
6. Modernizr.js [http://www.modernizr.com/](http://www.modernizr.com/) enables HTML5 compatibility with IE (and a dozen other great features)
7. IE-specific classes for simple CSS-targeting
8. iPhone/iPad/iTouch icon snippets 
9. Lots of other keen stuff...


## Sass Structure

Sass stylesheets are stored in _/stylesheets and output to /style.css

style.sass is made up of a number of key partials

1. Base - see Compass best practices (http://compass-style.org/help/tutorials/best_practices/)
2. HTML5 Reset - Sass version of HTML5 Reset Templates (https://github.com/murtaugh/HTML5-Reset)
3. Mixins - Don't repeat yourself, use sass mixins (http://sass-lang.com/)
4. Print - Print styles
5. Media - Responsive web design styles

  
## Changes to original HTML5 Reset Wordpress Theme

Added selectivizr.js and DOMAssistant.js for further IE support

Changed TEMPLATEPATH to bloginfo('template_url') - absolute path wasn't working on my server


## Installation

1. Download or clone the repo into your-site/wp-content/themes/ folder
2. Open Terminal
3. Run $ gem install compass
3. Open Terminal and navigate to your-site/wp-content/themes/HTML5-Reset-Compass-Sass-Wordpress-Theme/
4. Run $ compass watch
5. Open your-site/wp-content/themes/HTML5-Reset-Compass-Sass-Wordpress-Theme/_/stylsheets in your favourite text editor (Textmate?)
6. Code away…

## Demo

HTML5 Reset documentation can be found at: http://html5reset.org/

Demo site can be found at: http://html5reset.duckapp.co

## Notes  

This is built using .sass however can easily be converted to .scss if you prefer.