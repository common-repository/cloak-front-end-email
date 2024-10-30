=== Cloak Front End Email ===
Contributors: webbernaut
Donate link: https://www.paypal.me/webbernaut
Tags: email cloaking, front end email, javascript email, antispam, bot, crawl, e-mail, email, email address, encrypt, harvest, harvesting, hide, mail, mailto, obfuscate, protect, protection, robots, secure, security, spam, spambot, spider, protect email, email javaScript cloak
Requires at least: 3.0.1
Tested up to: 6.2.2
Stable tag: 1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Cloak / Obfuscate your email address with JavaScript in a simple short code [email].

== Description ==

Plugin is easy to use: just install it, use short code [email] and it just works. If you want multiple email addresses go to the settings page and create your custom email address and use the short code referenced. Will be similar to [email name="cfe-example"]. Simple to add a subject line with subject parameter in shortcode [email name="cfe-example" subject="My Email Subject Line"]

Simple and easy to use plugin for displaying your email on the front end of your website safely and securely through JavaScript cloaking / obfuscation. Email that is displayed is the email you have setup as your admin email in WordPress General Settings or the custom email you setup in on the "Cloak Email" settings page. Source code does not show your actual email address which keeps scrapers from email harvesting and putting you on their email spam lists.

== How it works ==

The Cloak Front End Email plugin follows the same methodology as [Cloudflare's email cloaking](https://developers.cloudflare.com/support/more-dashboard-apps/cloudflare-scrape-shield/what-is-email-address-obfuscation/). The email addresses are not in the source code of the website. Therefore any programing language that is doing a request to the site will scrape the source code of a website and since the email is not embedded in the source your emails will not be leaked. The plugin also protects against headless browsers PhantomJS, SlimerJS and Selenium that use default agent.

Source code will look like this (see screenshot below): 
&lt;span class="cfe-jsemail"&gt;&lt;a href="#"&gt;loading...&lt;/a&gt;&lt;/span&gt;

*Note: If users have javascript disabled on their browser they will get text saying "loading..." in place of the actual email.

** Color Customization **
Currently the email link will default to your global styles. We do not have a color setting yet but plan to in the future. Until then it's pretty easy to stylize the color with some very basic CSS.

Each email has a custom html class assigned to it so you could add css styles to your style sheet for each specific one (you would have to inspect the code in the browser to see what class is added, it’s dynamic so will depend on the email). It follows the same pattern as the shortcode name paramater [email name="cfe-bob"] 

.cfe-jsemail-cfe-bob a {color:#000;}

You can drop this code into the custom css module inside WordPress.

Appearance > Customize > Additional CSS

Another example with the default dashboard email [email]
&lt;span class=&quot;cfe-jsemail-cfe-dashboard&quot; data-subject=&quot;&quot;&gt;&lt;a href="#"&gt;loading...&lt;/a&gt;&lt;/span&gt;

.cfe-jsemail-cfe-dashboard a {color:#000;}

Or you can set the global style using the below CSS.

.cfe-wrapper a {color:#000;}

Optional
.cfe-wrapper a:hover {color:purple;}

== Screenshot ==

1. Sample Source Code, no email is leaked in source code

== Installation ==

1. install and activate the plugin on the Plugins page
2. use short code [email] or [email name="cfe-example"] or [email name="cfe-example" subject="My Email Subject Line"] in your pages, posts, or widgets where you want to display your mailto email address.

== Changelog ==

= 1.1 =
* Fixed bug, cloak front end email was making WordPress top admin to disappear in 4.3.1

= 1.2 =
* Reformatted Ajax call

= 1.3 =
* Added ability to have multiple email addresses. Includes an admin settings page for adding custom or additional email addresses for the front end of your website with an easy short code [email name="cfe-example"]. Now detects PhantomJS and SlimerJS agents and keeps emails safe from headless browsers.

= 1.4 =
* Fixed bug, allow dot in email address first.last@email.com

= 1.5 =
* Admin bug fix

= 1.6 =
* Database prefix update

= 1.7 =
* Subject Line added to shortcode attribute use as follows [email name="cfe-example" subject="My Email Subject Line"], enable javascript text changed to loading...

= 1.8 =
* Fixed browser compatibly issues

= 1.9 =
* Fixed browser compatibly issues with mailto link not opening mail client.

= 1.9.1 =
* Optimized admin email ajax call.

= 1.9.2 =
* Protects aganist headless browser Selenium.
* Admin Shortcode XXS fix.
* Administrator role is only user that can access settings page. (if you need other user roles to access settings page please submit a feature request)

= 1.9.3 =
* Rendering bug fix with multiple emails on a single page

= 1.9.4 =
* Multisite compatibly fix.

= 1.9.5 =
* Multisite compatibly fixes.
