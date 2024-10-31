<div style="padding:30px; color:blue">
<p style="text-align: justify;"><img src="/wp-content/plugins/panel-my-blog/panel_my_blog.png" title="Panel My Blog"><br>
<br>
<p style="text-align: justify;">Initially, <strong>PANEL-MY-BLOG</strong> is a plugin which was developed to dynamically manage 301 redirects, so broken links to articles on your Wordpress blog.</p>
<p style="text-align: justify;">It has been expanded a few features that surround these redirects. Indeed, the manager of a website can not doubt all calls to pages that do not exist on his blog. Some visitors become so ill-intentioned. That is why this plugin dynamically manages the blaclisting through HTACCESS file. Warning, so those who already have a file HTACCESS on their blog, although Wordpress does not generate. Because this file would be prepared manually crushed by that of <strong>Panel-My-Blog</strong> !</p>
It comes with four sub-menus :
<ol>
  <li>View all request for 404 error to optimize your web site</li>
	<li>Redirect 301 (dynamically manages the broken links after validation)</li>
	<li>Blacklist (maintains a list of hackers fontion a list of words to block in options)</li>
	<li>Options (manages a basic maintenance mode, the hotlinking...)</li>
	<li>This help menu <u>to read first</u></li>
</ol>
<p style="text-align: justify;">You should know that this plugin is installed normally but it is also fully manage its removal: it will leave your own database if you wanted to delete it from your plugins.</p>

<h3 style="text-align: justify;"><strong>REDIRECT 301</strong></h3>
<p style="text-align: justify;">... is composed of an array with three main columns, composed of a header line which is found in all sousenus plugin also:</p>
<ol>
	<li> <strong>"X"</strong> for removing the entry</li>
	<li><strong>"M"</strong> for input change</li>
	<li><strong>"TAG"</strong> that is le broken link of one to your articles</li>
  <li><strong>DROPDOWN LIST</strong> for 404 errors for automatic <strong>LINK</strong> below.</li>
  <li><strong>"LIEN"</strong> to inquire into full URL</li>
	<li><strong>"COUNT"</strong> that is the counter for the entry</li>
  <li> <strong> "BOT" </ strong> checkbox used to enregistrer in the virtual robot file links in removal request - error 410 </ li>
</ol>
<p style="text-align: justify;">For example, an article "www.monsite.fr/my-first-article.html" was deleted. When a robot or visitor will use this URL, a new entry will be made with <strong>TAG</strong> "/my-first-article.html" with <strong>CODE</strong> "404" (which indicate that this entry is new and the <strong>LINK</strong> will be filled only by the URL of your site.</p>
<p style="text-align: justify;">After intelligence <strong>LINK</strong>, you could very well make it point to a keyword that was included in the article, like "http://www.monsite.fr/tag/actualite" , the entry will go into <strong>CODE</strong> 301 after page validation. Unless you wish to delete this entry simply by clicking the button <strong>"X"</strong>. In this case, the entry will be deleted after page validation.</p>
<p style="text-align: justify;">Note that if you delete an entry, it should not surprise you that it is réinscrive later due to passage of robots.</p>
<p style="text-align: justify;">If you wish to notify the bots to remove this URL, put the <strong>LINK</strong> vacuum to pass the <strong>CODE</strong> to 410.</p>

<h3 style="text-align: justify;"><strong>BLACKLIST</strong></h3>
<p style="text-align: justify;">...is composed of a list of IP address automatically blackisted 403 prohibiting access to your site. You can delete and/or validate these address. This file automatically regenerates HTACCESS however if the option to enable this feature been checked in the submenu(1) below.</p>
<h3><strong>OPTIONS</strong></h3>
... the submenu is most important that manages:
<ol>
  <li>Manage virtual file ROBOTS.TXT<br>By default, all access are "Allow"<br>
  Add a new entry with the BOT file to enter some paramters into the last empty line<br>
  Complete the fields <strong>Bot</strong> and <strong>Allow</strong> or <strong>Disallow</strong><br>
  To delete an entry: one of empty fields and then submit the form<br>
  You have un test link  to your virtual file ROBOTS.TXT</li>
	<li>the ability to administer the file HTACCESS: this will activate the following options ... and the button "Generate HTACCESS"</li>
  <li>management virtual file <em>ROBOTS.TXT</em>: warning it should not be any file of that name at the root of your site.<br>
  Just enter your home directories as "/directory/" or file "/file.txt"<br>
  You can also enter the reference to your sitemap, as "sitemap: http://mysite/sitemap-name.all"</li>
 	<li>display a maintenance page when you want to make changes to your blog</li>
	<li>the list of allowed sites hotlinking to as "monsite.com". Please do not forget to include your own site</li>
	<li>ability to block if a visitor reaches a certain threshold of error-404</li>
	<li>possibility of replacing the image hotlinked by the URL of your site as an image</li>
	<li>management keyword filter hackers as a list of banned words in URLs</li>
	<li>a list of regular expressions in redirect that will fit into your HTACCESS. This however is reserved for connoisseurs advised!a list of regular expressions in redirect that will fit into your HTACCESS. This however is reserved for connoisseurs advised !</li>
</ol>
<p style="text-align: justify;"><em>For users of keywords, it is advisable to enter at least two keywords "plugin" and "theme".</em>
<strong>WARNING:</strong> not to put word to ban you (like "admin")</p>
<p style="text-align: justify;"><u>For the latter menu</u>, the "Save" button used to save these options by default, do nothing more.</p>
<p style="text-align: justify;">The button "Generate HTACCESS" your file will generate HTACCESS with options saved and blacklist lists, provided that they have checked mode "administration htaccess".</p>
<h3 style="text-align: justify;">The created files by <strong>PANEL-MY-BLOG</strong></h3>
<p style="text-align: justify;"><p style="text-align: justify;">At the root of your blog set version french or english depending on the language set:</p>
<ol>
	<li>.HTACCESS</li>
	<li>MAINTENANCE.HTML</li>
	<li>SECURITE.HTML</li>
	<li>NO_IMAGE.PNG</li>
</ol>
<h3 style="text-align: justify;">Laster informations</h3>
<p style="text-align: justify;">The button "Compact the table" is used to reindex the table in chronological order</p>
<br>
<em>Friendly, Bernard G.</em><br>
</div>