<div style="padding:30px; color:blue">
<p style="text-align: justify;"><img src="/wp-content/plugins/panel-my-blog/panel_my_blog.png" title="Panel My Blog"><br>
<br>
<p style="text-align: justify;">Au départ, <strong>PANEL-MY-BLOG</strong> est un plugin qui a été développé afin de gérer dynamiquement les redirections 301, donc les liens rompus d'articles de votre blog Wordpress.</p>
<p style="text-align: justify;">Il a été étoffé de quelques fonctionnalités qui entourent ces redirections. En effet, le gestionnaire d'un site internet ne peut se douter de tous les appels à des pages qui n'existent pas sur son blog. Certains visiteurs deviennent donc mal-intentionnés. C'est pour cela que ce plugin gère dynamiquement le blaclisting par le biais du fichier HTACCESS. Attention, donc à ceux qui possèdent déjà un fichier HTACCESS sur leur blog, bien que Wordpress n'en génère pas. Car ce fichier élaboré manuellement serait écrasé par celui de <strong>Panel-My-Blog</strong> !</p>
Il se décline avec quatre sous-menus :
<ol>
	<li>Redirect 301 (gère dynamiquement les liens rompus après validation)</li>
	<li>Blacklist (gère une liste de hackers en fontion d'une liste de mots à bloquer en options)</li>
	<li>Options (gère un mode maintenance de base, le hotlinking...)</li>
	<li>Ce menu d'aide <u>à lire avant toute chose</u></li>
</ol>
<p style="text-align: justify;">Il faut savoir que ce plugin s'installe normalement mais également il s'est gérer entièrement sa désinstallation: il laissera propre votre base de données si vous aviez envie de le supprimer de vos plugins.</p>

<h3 style="text-align: justify;"><strong>REDIRECT 301</strong></h3>
<p style="text-align: justify;">...est composé d'un tableau avec trois colonnes principales, composées d'une ligne d'entêtes qu'on retrouvera dans tous les sous-menus du plugin d'ailleurs :</p>
<ol>
	<li> <strong>"X"</strong> pour suppression de l'entrée</li>
	<li><strong>"M"</strong> pour modification de l'entrée</li>
	<li><strong>"TAG"</strong> ou le lien rompu d'un de vos articles,</li>
	<li><strong>LISTE DEROULANTE</strong> pour les erreurs 404 pour renseignement du <strong>LIEN</strong> ci-dessous.</li>
	<li><strong>"LIEN"</strong> à renseigner en URL complète</li>
	<li><strong>"COUNT"</strong> qui est le compteur de passage pour cette entrée, permettant de vérifier l'utilité de celle-ci</li>
  <li> <strong>"BOT"</ strong> check box for recording in the virtual robot file links in removal request - error 410 </ li>
</ol>
<p style="text-align: justify;">Pour exemple, un article "www.monsite.fr/mon-premier-article.html" a été supprimé. Lorsqu'un robot ou visiteur fera appel à cette URL, une nouvelle entrée sera ajoutée avec pour <strong>TAG</strong> "/mon-premier-article.html" avec le <strong>CODE</strong> "404" (qui indiquera que cette entrée est nouvelle et le champ <strong>LIEN</strong> sera uniquement remplie par l'URL de votre site.</p>
<p style="text-align: justify;">Après renseignement du <strong>LIEN</strong>, vous pourriez très bien le faire pointer sur un mot-clé qui avait été inclus dans l'article, du style "http://www.monsite.fr/tag/actualite", l'entrée passera en <strong>CODE</strong> 301 après validation de la page. A moins que vous ne désiriez supprimer tout simplement cette entrée en cliquant sur le bouton <strong>"X"</strong>. Dans ce cas, l'entrée sera supprimée après validation de la page.</p>
<p style="text-align: justify;">Notez bien que dans le cas où vous supprimez une entrée, il ne faut pas vous étonner que celle-ci se réinscrive ultérieurement du fait de passage de robots.</p>
<p style="text-align: justify;">Si vous désirez aviser les robots de supprimer cette URL, mettez le champ <strong>LIEN</strong> à vide afin de passer le <strong>CODE</strong> à 410</p>

<h3 style="text-align: justify;"><strong>BLACKLIST</strong></h3>
<p style="text-align: justify;">... est composé d'une liste d'adresse IP blackistée automatiquement avec interdition d'accès 403 à votre site. Vous pouvez en supprimer et/ou valider ces adresses IP. Cela regénère automatiquement le fichier HTACCESS si toutefois l'option d'activation de cette fonctionnalité à bien été cochée dans le sous-menu(1) qui suit.</p>
<h3><strong>OPTIONS</strong></h3>
... est le menu le plus important qui gère:
<ol>
  <li>la gestion du fichier virtuel ROBOTS.TXT<br>Par défaut, tous les accès sont en "Allow"<br>
  Pour ajouter une ligne dans votre fichier robot, modifier la dernière ligne vide<br>
  Compléter les deux champs <strong>Robot</strong> et <strong>Allow</strong> ou <strong>Disallow</strong><br>
  Pour supprimer une entrée: vider l'un des champs puis valider le formulaire<br>
  Vous avez un lien de test de votre virtuel ROBOTS.TXT</li>
	<li>la possibilité d'administrer le fichier HTACCESS: cela activera les options suivantes... et le bouton "Générer HTACCESS"</li>
  <li>la gestion du <em>ROBOTS.TXT</em> virtuel : attention il ne doit pas y avoir de fichier de ce nom à la racine de votre site.<br>
  il suffit d'entrer vos répertoires sous la forme "/dossier/" ou fichiers "/fichier.txt"<br>
  Vous pouvez également y entrer la référence à votre sitemap, sous la forme "sitemap: http://monsite/nom-du-sitemap.complet"</li>
	<li>l 'affichage d'une page de maintenance lorsque vous désirez effectuer des modifications de votre blog</li>
	<li>la liste des sites autorisés au hotlinking sous la forme "monsite.com". Attention à ne pas oublier d'y inclure votre propre site</li>
	<li>possibilité de remplacer l'image hotlinkée par l'URL de votre site sous forme d'image</li>
	<li>possibilité de bloquer un visiteur s'il atteint un certain seuil d'error-404</li>
	<li>gestion des mots-clé filtre anti-hackers sous forme d'une liste de mots interdits dans les URL</li>
	<li>une liste d'expressions régulières de redirection qui va s'insérer dans votre HTACCESS. Cela est réservé toutefois aux connaisseurs avisés !</li>
</ol>
<p style="text-align: justify;"><em> Pour les utilisateurs des mots-clés, il est conseillé d'entrer au moins ces deux mots-clé "plugin" et "theme".</em>
<strong>ATTENTION:</strong> à ne pas mettre de mot qui puisse vous bannir (style "admin")</p>
<p style="text-align: justify;"><u>Pour ce dernier menu</u>, le bouton "Enregistrer" servira à enregistrer ces options par défaut, sans rien faire de plus.</p>
<p style="text-align: justify;">Le bouton "Générer HTACCESS" va générer votre fichier HTACCESS en fonction des options enregistrées et de la listes de blacklist, sous condition d'avoir coché le mode "administration du HTACCES".</p>
<h3 style="text-align: justify;">Les fichiers créés par <strong>PANEL-MY-BLOG</strong></h3>
<p style="text-align: justify;"><p style="text-align: justify;">A la racine définie de votre blog en version français ou anglais selon la langue définie:</p>
<ol>
	<li>.HTACCESS</li>
	<li>MAINTENANCE.HTML</li>
	<li>SECURITE.HTML</li>
	<li>NO_IMAGE.PNG</li>
</ol>
<h3 style="text-align: justify;">Dernières informations</h3>
<p style="text-align: justify;">Le bouton "Compacter la table" sert à réindexer la table par ordre chronologique</p>
<br>
<em>Amicalement, Bernard G.</em><br>
</div>