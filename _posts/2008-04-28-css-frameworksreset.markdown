--- 
date: 2008-04-28 11:12:17 +0100
layout: post
title: CSS Frameworks/Reset
wordpress_id: 46
wordpress_url: http://www.olliekav.com/?p=46
categories: 
  title: web standards
  slug: web-standards
  autoslug: web-standards
---

I find this quite an interesting subject at the moment, there seems to be two contrasting views to what works well and it was brought up again at the Future of Web Design in both the conference talk by [Jon Hicks](http://www.hicksdesign.co.uk/) and mentioned briefly by [Jina Bolton](http://jinabolton.com/ "Jina Bolton") and [Andy Clarke](http://www.stuffandnonsense.co.uk/ "Stuff and Nonsense") in Jina's workshop on CSS.  I am someone that comes from the school of liking to reset all my styles. 

Originally I used the... 
``* { margin:0; padding:0; }``
global reset but of course this can cause all sorts of trouble with certain elements (not that I knew this when I was first learning.) I tried out the yahoo framework but didn't really like it and then came upon [Eric Meyers ](http://meyerweb.com/eric/tools/css/reset/ "Eric Meyer") CSS reset which I now use for everything, plus a few of my own extra resets. I can understand [Jonathan Snook's](http://snook.ca/archives/html_and_css/no_css_reset/) views on why he dosen't use frameworks/resets and his reason's behind this, this comment interested me a lot
<cite>"One of the principles I took away from the Web Standards community was the concept that pixel perfect precision across the various rendering engines was impractical and a remnant of the table-based layouts of yesteryear. With CSS and progressive enhancement, it was okay that things might look a little different from one browser to the next because of variations in what they supported."</cite>
Now I'm really anal about things being even slightly out, a few pixel's and I am tearing my hair out trying to find the culprit (mostly in IE), but this quote got me thinking that maybe it's okay to do it, would clients ever really notice a few pixel's alignment on certain elements? Do they ever see it in all browser's? What are other people's views on this? Are we causing ourselves more work by getting so picky?! Personally I think I'll keep to using my framework and resets and making sure is perfect, I would find it really difficult to let go but completely understand why some people wouldn't do this.
