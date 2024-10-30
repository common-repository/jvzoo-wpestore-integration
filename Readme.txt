=== JVZoo WPestore integration ===
Contributors: cimon77
Tags: jvzoo, wpestore, jvzoo wpestore integration
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin integrates JVZoo and WPestore plugin

== Description ==
JVZoo WPestore integration plugin integrates JVZoo and WPestore plugin.
First, the JVZoo product id and WPestore product id are mapped with each other in the plugin. Then, after the purchase is made from JVZoo the purchase information is transferred to your site using JVZIPN (https://jvzoo.zendesk.com/hc/en-us/articles/206456857-JVZIPN-How-to-create-your-own-integrated-script) and stored in the WPestore via JVZoo WPestore integration plugin.

The emember is automatically created on your site and the login credentials are sent to their email.

Also, the download link of the product purchased from jvzoo is also sent to their email.

Instruction on using JVZoo WPestore integration plugin:
Make sure that Wpemember plugin and WPestore plugin are activated.

1) Go to the admin dashboard.
2) Goto JVZoo WPestore Settings -> JVZIPN Secret Key. (Hover on the info icon to get more information on how to retrieve JVZIPN secret key).
3) Enter the JVZipn Secret Key and save the secret key.

4) Goto JVZoo WPestore Settings -> Map Product-> Add new
5) Enter WP estore product id and JVZoo product id.
6) Click Save

7) Goto JVZoo WPestore Settings -> Settings
8) Enter WP eMember secret word and hit submit.
9) Enter WP eMember Membership level and hit submit. (This will change the membership level to the selected membership level of the user after purchase is made from jvzoo)

10) Create a new page in your WordPress. JVZIPN will send data via HTML FORM POST to this URL after the purchase is made. Create the page name which is not easy to guess.
You need to place this URL on JVZIPN URL on your jvzoo.
11) Place the shortcode [SS-JVZoo-estore] on this page. After the purchase is made from JVZoo the data are sent to this page and the customers are added to WPestore. The Cipher (Validation Processing Code) encryption is used to validate the post data.

== Installation ==
Automatic Plugin Installation

To add a JVZoo WPestore integration Plugin using the built-in plugin installer:
1) Go to Plugins > Add New.
2) Type in the name jvzoo wpestore integration in Search Plugins box 
3) Click Install Now to install the WordPress Plugin.
4) The resulting installation screen will list the installation as successful or note any problems during the install.
5) Click Activate Plugin to activate it.

To install a WordPress Plugin manually:

1) Download your WordPress Plugin to your desktop from ....
2) If downloaded as a zip archive, extract the Plugin folder to your desktop.
3) With your FTP program, upload the Plugin folder to the wp-content/plugins folder in your WordPress directory online.
4) Go to Plugins screen and find the JVZoo WPestore integration Plugin in the list.
5) Click Activate to activate it.

== Frequently Asked Questions ==
Does this plugin require wpestore plugin?
-Yes

Does this plugin require wpemember plugin?
-Yes

== Screenshots ==
1. Jvzipn secret key
2. Map products of JVZoo and WPestore
3. Settings

== Changelog ==
[July 18, 2017] Version 1.0.0 
[+] Initial Version
