=== amtyThumb ===
Contributors: Amit Gupta
Donate link: http://thinkzarahatke.com/
Tags: thumbnail, amty, image, first-image, Youtube, Vimeo, Dailymotion, Metacafe, Veoh
Requires at least: 2.5
Tested up to: 3.2
Stable tag: 2.0

Fetch first image of a post and Resize it. Otherwise resize an image.

== Description ==

This plugin lets you fetch first image of any post. It is fully configurable. You can crop an image or resize on the basis of its height, or width, or both.

Ataached/uploaded images might not be displayed in case of localhost.

Features over other plugins:

1. Can extract an image which is either on same server or on remote server
2. Can extract attached images
3. It can fetch first image from the post even if it is a video from 
	a) Youtube
	b) Vimeo
	c) Dailymotion
	d) Metacafe
	e) Veoh
4. You can use it to resize/crop an image instead of fetching it from any post.

For live example visit [article-stack](http://article-stack.com/ "amty thumb ")
			[TZH](http://thinkzarahatke.com/ "amty thumb ")
			

== Installation ==

Installation of plugin is similar to other wordpress plugin.

e.g.

1. Extract `amtyThumb.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


You may also add amtyThumb_post anywehre in your post or page using short code. For his;

To fetch image from specific post. Resize to half.
	[amtyThumbOnly percent=50 post_id=282]
To fetch image from current post. Resize by its width.(adjust height automatically)
	[amtyThumbOnly width=50]

For any doubt or query visit [article-stack](http://article-stack.com/ "amty thumb") or 


== Frequently Asked Questions ==


For more queries visit [article-stack](http://article-stack.com/ "amty thumb")


== Screenshots ==

For live example visit [article-stack](http://article-stack.com/ "amty thumb ")
			[THZ](http://thinkzarahatke.com/ "amty thumb ")
== Changelog ==

= 2.0 =
* security - image resizing utility can be called internally using post id instead of independent url. So noone else can use your bandwidth.
* caching - It caches first image url of all posts and tagged with post itself to save runtime processing.
* filesystem - Now it caches resized images on your site itself. It saves repeated on the fly resizing thus high CPU and memory.


= 1.2.1 =
* 404 error resolved

= 1.2 =
* Performance Improvement.
* Support for more video services like dailymotion,metacafe,veoh,vimeo etc.

= 1.1 =
* missing code is added to fetch uploaded and attached images.

== Upgrade Notice ==


= 2.0 =
* security - image resizing utility can be called internally using post id instead of independent url. So noone else can use your bandwidth.
* caching - It caches first image url of all posts and tagged with post itself to save runtime processing.
* filesystem - Now it caches resized images on your site itself. It saves repeated on the fly resizing thus high CPU and memory.

= 1.2.1 =
* 404 error resolved