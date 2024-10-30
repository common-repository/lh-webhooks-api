=== LH Webhooks Api ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-webhooks-api
Tags: webhooks, rest, api, IFTTT, maker
Requires at least: 3.0.
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds an API for adding your own webhooks in WordPress

== Description ==
This plugin allows you to create and delete token authenticated webhook enpoints for WordPress users (as many as you need). 

It also comes with a programming api to define methods which is the action is taken on the data posted to said endpoints.

== Frequently Asked Questions ==

= How do I programatically define a new method for handling data posted to the webhooks? =
* Each webhook msut have a method, this is passed to the endpoint as a GET string i.e. ?method=my_post_handler.
What happens to the posted data is defined by you programically by you. An example method 'log_via_email' is defined in the code (with explanatory comments).

The first step is to define the allowed methods, via the lh_webhooks_api_allowed_methods filter.

The second step is to define what happens to the posted data. this is done via a variable filter, i.e. lh_webhooks_api_**** , where the **** portion of the filter name is the name of your method. in the example this is 'lh_webhooks_api_log_via_email'.



== Installation ==

1. Upload the entire `lh-webhooks-api` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Navigate to Settings->Webhooks and create endpoints


== Changelog ==

**1.00 March 20, 2018**  
Initial release.


