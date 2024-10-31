=== Panel My Blog ===
Contributors: Bryan-Onyx
Donate link: http://www.bernard-g.fr/
Tags: 404, redirection, link, links, hotlinking, htaccess, security, robots.txt, firewall
Requires at least: 3.4.0
Tested up to: 4.0.0
Stable tag: 2.7.1

== Description ==         

PANEL-MY-BLOG is a plugin developed at the beginning in order to manage redirections 301 dynamically,
thus broken bonds of articles of your blog Worpress when your website has aged well.
Also this plugin is very convenient to manage the virtual file Robots.txt<br />
For information, attack your website takes a few milliseconds but will be blocked by this plugin.<br />
<br />
<em>A new functionality has just been included with the automatic banishment (see “Other Notes”)</em><br />
<br />
It was packed of some functionalities which surrounds these redirections.<br />
<br />
It is declined with three sub-menus: <br />
- Redirect 301 (dynamically manages the broken bonds) <br />
- Blacklist (manages a list of hackers according to a list of words to block in option) <br />
- Options (a mode basic maintenance, the hotlinking manages…)<br />
and also manage your virtual robots.txt<br />
<br />
It should be known that this self-upgrade plugin and <br />
also self-cleaner: it will leave clean your database<br />
if you wanted to remove it your plugins.<br />
<br />
*** The FILES CREATE by PANEL-MY-BLOG *** <br />
With the root of your blog: <br />
- the file .HTACCESS<br />
- the file MAINTENANCE.HTML<br />
- the file SECURITE.HTML<br />
- the file NO_IMAGE.PNG<br />

== Installation ==

1. Upload directory `panel-my-blog` to the `/wp-content/plugins/` directory<br />
2. Activate the plugin through the 'Plugins' menu in WordPress<br />

== Frequently Asked Questions ==

* In order to test the plugin, to enter false a URL of your site<br />
in your address's field of your browser.<br />
View then the result in "Redirect 301" <br />
* For other questions have a look in the help menu of the plugin when it is installed<br>
* No frequently askes questions at this time

== Screenshots ==

1. Manage Virtual ROBOTS.TXT
2. Redirect 301
3. Manage Hotlinking
4. Attack your website takes a few milliseconds

== Changelog ==

= 2.7.1 =
* Tested with CMS 4.0

= 2.7.0 =
* Tag_Base modified for use

= 2.6.8 =
* Notify bots from 410 errors

= 2.6.7 =
* Tested Wordpress 3.9.0
* Emptying of the field "link" if the "bot" box is checked

= 2.6.6 =
* Tested Wordpress 3.8.1

= 2.6.5 =
* version oriented smartphone

= 2.6.4 =
* Tested with Basie
* Preview HTACCESS file

= 2.6.2 =
* fixed bug in the rule against 410 errors in the robots.txt

= 2.6.1 =
* Tested new release Wordpress

= 2.6.0 =
* New menu for options virtual robots.txt
* Possibility adding entry 404 or 410 in virtual robots.txt

= 2.5.3 =
* Optimize code for virtual robots.txt

= 2.5.2 =
* Optimize .htaccess

= 2.5.1 =
* Custom text of maintenance
* Use jQuery code

= 2.5.0 =
* Major version.

= 2.4.8 =
* Various change.

= 2.4.7 =
* Officially tested with such OVH hosts or 1and1 Internet...

= 2.4.6 =
* Tested with Wordpress 3.5.1

= 2.4.4 =
* For example, attack your website takes a few milliseconds (see screenshots)

= 2.4.3 =
* Various change.

= 2.4.2 =
* Blocking IP address after three requests abnormal error 404

= 2.3.9 =
* Add a field of the address IP in order to see which generates errors 301
* Add new managment fields "allow" and "disallow" for the virtual file robots.txt

= 2.3.8 =
* Adding robots tag "index, follow" for some bots at virtuel file robots.txt

= 2.3.5 =
* change pseudonym to Web site

= 2.3.2 =
* Fix manage delete IP address

= 2.3.1 =
* Possibility to add manuel IP address to block into sub-menu "blacklisting"

= 2.3.0 =
* Performing managment virtual file ROBOTS.TXT

= 2.2.14 =
* Adding a button to empty all entries Redirection 301

= 2.2.12 =
* New little feature from the menu BLACLINSTING

= 2.2.10 =
* Using the Wordpress translation system

= 2.1.0 =
* Basic version after extensive testing.

== Upgrade Notice ==

= 2.2.14 =
* Adding a button to empty all entries Redirection 301

= 2.1.3 =
* Plugin translated french or english depending on the variable WPLANG 

= 2.2.0 =
* Possibility of enable or disable the management of HTACCESS

== Arbitrary section ==
The fact that a visitor uses a URL containing the keywords to be banned,
the visitor is automatically blacklisted because the htaccess file is generated.
The administrator of your blog is notified by mail.<br>
<em>Careful not to blacklist yourself by testing</em><br>
you can find 404 errors with searches containing keywords, it is that robots can not be banned.<br>
<strong>See also the help-item !!!</strong><br>