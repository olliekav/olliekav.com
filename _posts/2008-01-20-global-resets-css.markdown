--- 
date: 2008-01-20 05:46:29 +0000
layout: post
title: Global resets CSS
wordpress_id: 17
wordpress_url: http://www.olliekav.co.uk/blog/2008/01/20/global-resets-css/
categories: 
  title: web standards
  slug: web-standards
  autoslug: web-standards
---
[http://meyerweb.com/eric/thoughts/2008/01/15/resetting-again/](http://meyerweb.com/eric/thoughts/2008/01/15/resetting-again/)This is a really interesting article from Eric Meyer, I have often used the basic* {margin:0;padding:0;border:0;etc}In my CSS markup, it makes so much more sense to get very specific with the reset and make sure everything is covered.html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, font, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;}body {line-height: 1;}ol, ul {list-style: none;}blockquote, q {quotes: none;}/* remember to define focus styles! */:focus {outline: 0;}/* remember to highlight inserts somehow! */ins {text-decoration: none;}del {text-decoration: line-through;}/* tables still need 'cellspacing="0"' in the markup */table {border-collapse: collapse;border-spacing: 0;
