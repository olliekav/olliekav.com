--- 
date: 2008-11-15 20:51:30 +0000
layout: post
title: Photo montage with Safari/Chrome/Webkit, JQuery and Flickr...
wordpress_id: 141
wordpress_url: http://www.olliekav.com/?p=141
categories: 
  title: art
  slug: art
  autoslug: art
---
**This only works in Safari and Chrome**There is no doubting how powerful [Webkit](http://webkit.org/) is, some of the new CSS selectors it provides make it a breeze to do certain things that would have normally required a sea of div's, javascript or Flash to make them work. <!--more-->Couple this with [JQuery](http://jquery.com/) and the possibilities become endless. As part of something I wanted to try out I decided to create a photo montage using the [JQuery Flickr plugin](http://code.google.com/p/jquery-flickr/) to pull in my photo's from [Flickr](http://www.flickr.com/photos/olliekav/), then use the -webkit-transform and -webkit-transition selectors CSS3 selectors within webkit to manipulate the photos to create a montage and that the photos could be animated on hover. A flash like effect all using CSS. You can view the results here...**[Webkit Flickr Photo Montage](http://www.olliekav.com/wp-content/uploads/webkitmontage/)**The Flickr plugin code was altered slightly to append a different class name to each item picture brought in, the following lines...(77)``list.append('< li >< a href ="'+h+'" '+s.attr+' title="'+photo['title']+'">< img src="'+t+'" alt="'+photo['title']+'" class=\"number' + i + ' animate\"/ >< />');``By adding class=\"number' + 1 +' animate\"/ we can style each corresponding img and the animate class holds the [Webkit](http://webkit.org/) selectors. You don't even have to use Flickr to do this, just create individual class names for the images you want to use in your image folder. Because the images are positioned absolutely within the list elements the placement can be changed on the fly.The source code can be downloaded from **[here](http://www.olliekav.com/examples/webkitmontage/flickr-montage.zip)**, it is available on the creative commons license. The CSS could definitely be compressed a bit more but I have left it as it so you can make your own decision on that. It is easily customizable.  Post up any interesting photo montages you come up with!
