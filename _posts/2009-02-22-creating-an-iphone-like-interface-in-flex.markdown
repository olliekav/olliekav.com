--- 
date: 2009-02-22 02:26:30 +0000
layout: post
title: Creating an iPhone interface in Flex 3
wordpress_id: 228
wordpress_url: http://www.olliekav.com/?p=228
categories: 
  title: Flex/Flash
  slug: flexflash
  autoslug: flex/flash
---
As part of my recent design work I have been able to get involved in some Flex development and went on a course to explore it a bit further. I have used Flash for a while but not regualry so jumped at the chance to learn more about what Flex can do. <!--more-->
One of the first observations, especially being a designer and front end developer is it is very, very developer based. I have had to go though quite a learning curve to get my head into the mindset of a developer and learn a lot of action script!
This is the latest design for a our beta site, we went through quite a few iterirtions from user testing, it's a mix of XHTML/CSS/JQuery and Flex and has been an amazing experience seeing how we could make these technologies work together. One part of the design I did involved the use of an Accordion component overlayed on the map that would hold latest local data. On click of these items it would operate very similiar to the iPhone and the panel would slide left to reveal extended content about that section. ![TouchLocal Area page](http://www.olliekav.com/wp-content/uploads/2009/02/areapage-260109.jpg "TouchLocal Area page")Seeing as this is something I could not find very much documentation about I thought I would provide the code myself and [Romain Eude](http://blog.thecodingfrog.com/) (The 'G' Rails/flex developer) built and style it a little differently so it looked just like the iPhone!
**Here you can see the results**

**[Open in a new browser window to view source](http://www.olliekav.com/wp-content/uploads/swf/custom-accordion/CustomAccordion.html)**
[kml_flashembed fversion="8.0.0" replaceId="accordonFlex" movie="http://www.olliekav.com/wp-content/uploads/swf/custom-accordion/CustomAccordion.swf" targetclass="flashmovie" useexpressinstall="true" publishmethod="dynamic" width="100%" height="600"]
[![Get Adobe Flash player](http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif)](http://adobe.com/go/getflashplayer)
[/kml_flashembed]It uses a really powerful feature of Flex which is states, tie this in with a smooth transition and slide a panel across its y axis and you get the iPhone interface to a tee. With a little advanced styling and the adding of the [CanvasButtonAccordionHeader](http://flexlib.googlecode.com/svn/trunk/examples/CanvasButtonAccordionHeader/CanvasButtonAccordionHeader_Sample.swf) from the [Flexlib](http://code.google.com/p/flexlib/) project written by [Doug McCune](http://dougmccune.com/blog/) you get a heavily customizable Accordion with dual states to display extended information.A lot of thanks has to go to [Romain Eude](http://blog.thecodingfrog.com/) for tidying up my rookie flex code and streamlining this. It could probably be tidied up more but I'll leave that up to anyone who wants to use this.You'll be able to see it in full action on [TouchLocal.com](http://beta.touchlocal.com) in the coming month once all our design changes are complete.
**[Open in a new browser window to view source](http://www.olliekav.com/wp-content/uploads/swf/custom-accordion/CustomAccordion.html)**
