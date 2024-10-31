<?php
if ( !class_exists("admin_activate")) {
  class admin_activate {
    var $dir, $url, $base;
    var $fichier, $temp;
    var $serveur, $maison;
    var $mode_maintenance;
    var $version, $visite;

    function __construct() {
      global $wpdb;
      $this->dir = plugin_dir_path( __FILE__ );
      $this->tb_iplist = $wpdb->prefix . 'gestblog_iplist';
    }
    
    public function maj_access($envoi = FALSE) {
      $this->url = get_site_url();
      $this->serveur = "http://" . $_SERVER['HTTP_HOST'];
      
      if ( $this->url != $this->serveur) {
        $this->maison = substr( $this->url, strlen($this->serveur));
        if ( substr($this->maison, -1, 1) != "/" ) $this->maison .= "/";
      } else {
        $this->maison = "/";
      }

      $this->options = get_option('_GestBlog_settings');
      $this->fichier = fopen($_SERVER['DOCUMENT_ROOT'].$this->maison.".htaccess","w");
      flock($this->fichier,LOCK_EX);
      $this->temp = "# Fichier généré le " . date("d-m-Y"). " à ". date("H:i:s") . "\n";
      fputs($this->fichier,$this->temp);
      // Protection de recherche de fichier
      $this->temp = "SetEnv REGISTER_GLOBALS 0\n";
      $this->temp .= "SetEnv PHP_VER 5_3\n";
      $this->temp .= "ServerSignature Off\n";
      $this->temp .= "Options All -Indexes\n";
      $this->temp .= "SetEnv ZEND_OPTIMIZER 1\n";
      $this->temp .= "CheckSpelling off\n";
      $this->temp .= "Options +FollowSymLinks\n";
      $this->temp .= "AddType x-mapp-php5 .php\n";
      $this->temp .= "AddType image/svg+xml svg svgz\n";
      $this->temp .= "AddEncoding gzip svgz\n";
      $this->temp .= "AddType application/vnd.ms-fontobject eot\n";
      $this->temp .= "AddType application/x-font-ttf ttf ttc\n";
      $this->temp .= "AddType font/opentype otf\n";
      $this->temp .= "AddType application/x-font-woff woff\n\n";
      fputs($this->fichier,$this->temp);
      // Protection du wp-config.php
      $this->temp = "# Protection du wp-config.php\n";
      $this->temp .= "<Files ".$this->maison."wp-config.php>\n";
      $this->temp .= "\tOrder deny,allow\n";
      $this->temp .= "\tdeny from all\n";
      $this->temp .= "</Files>\n\n";
      fputs($this->fichier,$this->temp);
      // Protection du htaccess
      $this->temp = "# Protection du htaccess\n";
      $this->temp .= "<Files ".$this->maison.".htaccess>\n";
      $this->temp .= "\tOrder deny,allow\n";
      $this->temp .= "\tdeny from all\n";
      $this->temp .= "</Files>\n\n";
      fputs($this->fichier,$this->temp);
      // Block access to backup and source files.
      $this->temp = '# Block access to backup and source files.'."\n";
      $this->temp .= '<FilesMatch "(^#.*#|\.(bak|config|dist|fla|inc|ini|log|psd|sh|sql|sw[op])|~)$">'."\n";
      $this->temp .= "\t".'Order allow,deny'."\n";
      $this->temp .= "\t".'Deny from all'."\n";
      $this->temp .= "\t".'Satisfy All'."\n";
      $this->temp .= '</FilesMatch>'."\n\n";
      fputs($this->fichier,$this->temp);
      // Gestion des règles de réécriture
      $this->temp = "# Règles de réécriture\n";
      $this->temp .= "# BEGIN WordPress\n";
      $this->temp .= "\tRewriteEngine On\n";
      $this->temp .= "\tRewriteBase ".$this->maison."\n";
      $this->temp .= "\tRewriteRule ^index\.php$ - [L]\n";
      $this->temp .= "\tRewriteCond %{REQUEST_FILENAME} !-f\n";
      $this->temp .= "\tRewriteCond %{REQUEST_FILENAME} !-d\n";
      $this->temp .= "\tRewriteRule . ".$this->maison."index.php [L]\n";
      $this->temp .= "# END WordPress\n\n";
      fputs($this->fichier,$this->temp);
      // Gestion des hackers
      if ( $this->options['blacklist'] == "1" ) $this->genere_blacklist();
      // Gestion des domaines
      if ( !empty($this->options['domaine']) ) $this->genere_domaine();
      // Gestion du mode maintenance
      if ( $this->options['maintenance'] == "on" ) $this->genere_maintenance();
      // Blocage du hotlinking
      if ( !empty($this->options['hotlink']) ) $this->genere_hotlink();
      // Gestion des expirations de fichiers
      $this->temp = '# Gestion expiration cache'. "\n";
      $this->temp .= '<IfModule mod_expires.c>'."\n";
      $this->temp .= "\t".'ExpiresActive On'."\n";
      $this->temp .= "\t".'ExpiresDefault "access plus 2 days"'."\n";
      $this->temp .= "\t".'ExpiresByType image/jpg "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType image/jpeg "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType image/gif "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType image/png "access 1 year"'."\n";
      $this->temp .= "\t".'ExpiresByType text/css "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType application/pdf "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType text/x-javascript "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType application/x-shockwave-flash "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType application/javascript "access 1 month"'."\n";
      $this->temp .= "\t".'ExpiresByType image/x-icon "access plus 1 week"'."\n";
      $this->temp .= "\t".'ExpiresByType application/atom+xml "access plus 1 hour"'."\n";
      $this->temp .= "\t".'ExpiresByType application/rss+xml "access plus 1 hour"'."\n";
      $this->temp .= "\t".'ExpiresByType text/html "access plus 0 seconds"'."\n";
      $this->temp .= '</IfModule>'."\n\n";
      fputs($this->fichier,$this->temp);
      //  BEGIN Cache-Control Headers
      $this->temp = '# BEGIN Cache-Control Headers'. "\n";
      $this->temp .= '<IfModule mod_headers.c>'. "\n";
      $this->temp .= "\t".'<FilesMatch "\\.(ico|jpe?g|png|gif|swf|css|gz)$">'. "\n";
      $this->temp .= "\t\t".'Header set Cache-Control "max-age=2592000, public"'. "\n";
      $this->temp .= "\t".'</FilesMatch>'. "\n";
      $this->temp .= "\t".'<FilesMatch "\\.(js)$">'. "\n";
      $this->temp .= "\t\t".'Header set Cache-Control "max-age=2592000, private"'. "\n";
      $this->temp .= "\t".'</FilesMatch>'. "\n";
      $this->temp .= "\t".'<filesMatch "\\.(html|htm)$">'. "\n";
      $this->temp .= "\t\t".'Header set Cache-Control "max-age=7200, public"'. "\n";
      $this->temp .= "\t".'</filesMatch>'. "\n";
      $this->temp .= "\t".'# Disable caching for scripts and other dynamic files'. "\n";
      $this->temp .= "\t".'<FilesMatch "\.(pl|php|cgi|spl|scgi|fcgi)$">'. "\n";
      $this->temp .= "\t\t".'Header unset Cache-Control'. "\n";
      $this->temp .= "\t".'</FilesMatch>'. "\n";
      $this->temp .= '</IfModule>'. "\n";
      $this->temp .= '# END Cache-Control Headers'. "\n\n";
      fputs($this->fichier,$this->temp);
      // CORS-enabled images
      $this->temp = '# CORS-Enabled images'."\n";
      $this->temp .= '<IfModule mod_setenvif.c>'."\n";
      $this->temp .= "\t".'<IfModule mod_headers.c>'."\n";
      $this->temp .= "\t\t".'<FilesMatch "\.(gif|ico|jpe?g|png|svg|svgz|webp|otf|eot)$">'."\n";
      $this->temp .= "\t\t".'SetEnvIf Origin ":" IS_CORS'."\n";
      $this->temp .= "\t\t".'Header set Access-Control-Allow-Origin "*" env=IS_CORS'."\n";
      $this->temp .= "\t\t".'</FilesMatch>'."\n";
      $this->temp .= "\t".'</IfModule>'."\n";
      $this->temp .= '</IfModule>'."\n\n";
      fputs($this->fichier,$this->temp);
      // Etags
      $this->temp = '<IfModule mod_headers.c>'."\n";
      $this->temp .= "\t".'Header unset ETag'."\n";
      $this->temp .= '</IfModule>'."\n";
      $this->temp .= 'FileETag None'."\n\n";
      fputs($this->fichier,$this->temp);
      // Fermeture du fichier HTACCESS
      fclose($this->fichier);
      // Envoi d'un mail à l'administrateur
      if ($envoi == TRUE && $this->options['avert'] == "0") {
        $admin_blog = get_option('admin_email');
        $headers = "From: ".$admin_blog."\n";
        $headers .= "Reply-To: ".$admin_blog."\n";
        $headers .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $contenu = _e("New IP address has been blacklisted.","panel-my-blog") . "<br />";
        $contenu .= _e("Please to review your blog ","panel-my-blog") . $this->url;
        mail($admin_blog, 'PANEL MY BLOG', $contenu, $headers);
      }
    }

    function genere_blacklist() {
      $this->options = get_option('_GestBlog_settings');
      $this->temp = "#Blacklist des potentiels hackers\n";
      global $wpdb;
    	$sql = $wpdb->get_results("SELECT id, ip, count FROM `".$this->tb_iplist."`");
      foreach ( $sql as $valeur ) {
        $this->temp .= "deny from ".$valeur->ip."\n";
      }
      if ( !empty($sql) ) {
        $this->temp .= "ErrorDocument 403 ".$this->maison."securite.html\n";
        $this->temp .= "<Files securite.html>\n";
        $this->temp .= "\tallow from all\n";
        $this->temp .= "</Files>\n";
        $this->temp .= "#Fin de la blacklist\n\n";
        fputs($this->fichier,$this->temp);
        $securite = fopen($_SERVER['DOCUMENT_ROOT'].$this->maison."securite.html","w");
        $this->temp = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n";
        $this->temp .= "<html>\n<head>\n";
        $this->temp .= "<title>".__("Web site access denied","panel-my-blog")."</title>\n";
        $this->temp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
        $this->temp .= "</head>\n";
        $this->temp .= "<body bgcolor=\"#000\" text=\"#FFAD14\" style=\"text-align : center\">\n";
        $this->temp .= "<h1>".__("Sorry... our web site","panel-my-blog")."</h1>\n";
        $this->temp .= "<h1>".home_url()."</h1>\n";
        $this->temp .= "<h1>".__("is currently denied","panel-my-blog")."</h1>\n";
        $this->temp .= "<h1>".__("for reasons of security !!!","panel-my-blog")."</h1>\n";
        $this->temp .= "</body>\n</html>\n";
        fputs($securite,$this->temp);
        fclose($securite);
      }
      $wpdb->flush();
    }

    function genere_maintenance() {
      $this->options = get_option('_GestBlog_settings');
    	$my_ip = str_replace(".", "\.", $_SERVER["REMOTE_ADDR"]);
      $this->temp = "# Activation du mode maintenance\n";
      $this->temp .= "RewriteCond %{REQUEST_URI} !".$this->maison."maintenance.html$\n";
      $this->temp .= "RewriteCond %{REMOTE_ADDR} !^$my_ip\n";
      $this->temp .= "RewriteRule $ ".$this->maison."maintenance.html [R=302,L]\n\n";
      fputs($this->fichier,$this->temp);
      $this->mode_maintenance = fopen($_SERVER['DOCUMENT_ROOT'].$this->maison."maintenance.html","w");
      $this->temp = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n";
      $this->temp .= "<html>\n<head>\n";
      $this->temp .= "<title>".__("Website under maintenance","panel-my-blog")."</title>\n";
      $this->temp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
      $this->temp .= "<meta http-equiv=\"Refresh\" content=\"15;URL=index.php\">\n";
      $this->temp .= "</head>\n";
      $this->temp .= "<body bgcolor=\"#009933\" text=\"#FFAD14\" style=\"text-align: center\">\n";
      if (strlen ($this->options['message_view']) <= 2 ) {
        $this->temp .= "<h1>".__("Sorry, our web site","panel-my-blog")."</h1>\n";
        $this->temp .= "<h1>".home_url()."</h1>\n";
        $this->temp .= "<h1>".__("is under maintenance","panel-my-blog")."</h1>\n";
      } else {
        $this->temp .= "<h1>" . nl2br($this->options['message_view'])."</h1>\n";
      }
      $this->temp .= "<h2 style=\"color:white\">" .__("Come back in a few minutes to read !!!","panel-my-blog")."</h2>\n";
      $this->temp .= "<h3 style=\"color:grey\"><em>" . __("Thank you for your understanding","panel-my-blog")."</em></h3>\n\n";
      $this->temp .= "</body>\n</html>\n";
      fputs($this->mode_maintenance,$this->temp);
      fclose($this->mode_maintenance);
    }

    function genere_hotlink() {
      $this->options = get_option('_GestBlog_settings');
      $count_var = 0;
      $option = explode(chr(13).chr(10),$this->options['hotlink']);
      $this->temp = "# Blocage du hotlinking\n";
      $this->temp .= "RewriteCond %{HTTP_REFERER} !^$\n";
      foreach ($option as $valeur) {
        $valeur = trim($valeur);
        if ( $valeur != "" ) {
          $count_var ++;
          $valeur = str_replace("www.", "", $valeur);
          $valeur = str_replace(".", "\.", $valeur);
          $this->temp .= "RewriteCond %{HTTP_REFERER} !^http://(.+\.)?".$valeur."/.*$ [NC]\n";
        }
      }
      if ( $this->options['image'] == "on") {
        $this->genere_no_image();
        $this->temp .= "ReWriteRule .*\.(gif|jpe?g|jpg)$ ".$this->maison."no_image.png [L,NC]\n\n";
      } else {
        $this->temp .= "RewriteRule \.(jpg|gif|png)$ - [F]\n\n";
      }
      if ( $count_var != 0 ) fputs($this->fichier,$this->temp);
    }

    function genere_domaine() {
      $this->options = get_option('_GestBlog_settings');
      $count_var = 0;
      $option = explode(chr(13).chr(10),$this->options['domaine']);
      $this->temp = "# Blocage de domaine\n";
      foreach ($option as $valeur) {
        $valeur = trim($valeur);
        if ( $valeur != "" ) {
          $valeur = str_replace("www.", "", $valeur);
          if ( strpos($valeur,".") !== FALSE ) $valeur = substr($valeur,0,strpos($valeur,"."));
          $count_var ++;
          if ( $count_var == 1) {
            $web = $valeur;
          } else {
            $web .=  "|" . $valeur;
          }
        }
      }
      $this->temp .= "SetEnvIfNoCase Referer ". chr(34) . ".*(" . $web . ").*" . chr(34). " domaine=yes\n";
      $this->temp .= "Order allow,deny\n";
      $this->temp .= "\tallow from all\n";
      $this->temp .= "deny from env=domaine\n\n";
      if ( $count_var != 0 ) fputs($this->fichier,$this->temp);
    }

    function genere_no_image() {
      $this->options = get_option('_GestBlog_settings');
      $font = $this->dir. "/airstrip.ttf";
      $size = 12;
      $texte =  substr(home_url(),7);
      $rect = imagettfbbox($size, 0, $font, $texte);
      $largeur = $rect[2] - $rect[0] + 10;
      $hauteur = $rect[3] - $rect[5] + 10;
      $img = imagecreate($largeur, $hauteur);
      $bg = imagecolorallocate($img, 220, 220, 220);
      $black = imagecolorallocate($img, 0, 0, 0);
      $fTextColor = imagecolorallocate($img, 150, 150, 150);
      $textColor = imagecolorallocate($img, 255, 0, 0);
      imagettftext($img, $size, 0, 6, $hauteur - 9, $black, $font, $texte);
      imagettftext($img, $size, 0, 5, $hauteur - 10, $textColor, $font, $texte);
      imagepng($img, $_SERVER['DOCUMENT_ROOT'].$this->maison."no_image.png");
    }
  }
}
?>