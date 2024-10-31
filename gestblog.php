<?php
/*
Plugin Name: Panel My Blog
Plugin URI: http://wordpress.org/extend/plugins/panel-my-blog/changelog/
Description: Panel management redirects links lost or 404 error, blacklisting, maintenance mode and firewall with file htaccess
Version: 2.7.1
Author: <a href="http://www.bernard-g.fr/">Bernard G.</a>
Author URI: http://www.bernard-g.fr/
*/
/*  Copyright Bernard-G. */
if ( is_admin() ) {
  if ( !class_exists("GestBlog")) {
    class GestBlog {
      var $tb_301, $tb_301_new, $tb_iplist, $tb_iplist_new;
      var $dir, $url, $base;
      var $fichier, $temp, $trouve;
      var $serveur;
      var $mode_maintenance;
      var $structure_301, $structure_iplist;
      var $version, $visite, $options;
      /* Début du constructeur */
      function __construct() {
        include_once("misc.php");
        $this->version = "2.7.1";
        global $wpdb;
        $this->url = get_site_url();
        if ( substr($this->url, -1, 1) != "/" ) $this->url .= "/";
        $this->dir = plugin_dir_path( __FILE__ );
        $this->tb_301 = $wpdb->prefix . 'gestblog_301';
        $this->tb_301_new = $wpdb->prefix . 'gestblog_301_new';
        $this->tb_iplist = $wpdb->prefix . 'gestblog_iplist';
        $this->tb_iplist_new = $wpdb->prefix . 'gestblog_iplist_new';
        $this->tb_robot = $wpdb->prefix . 'gestblog_robot';
        $this->tb_robot_new = $wpdb->prefix . 'gestblog_robot_new';
        $this->options = get_option('_GestBlog_settings');
        if ( is_array($this->options['robot']) ) {
          echo '';
        } else {
          $tableau =  explode(chr(13).chr(10),$this->options['robot']);
          $new_options = array();
          foreach ($tableau as $key => $valeur) {
            $new_options[] = "*";
            $new_options[] = $valeur;
          }
          $this->options['robot'] = $new_options;
          unset($tableau);
          unset($new_options);
          update_option('_GestBlog_settings', $this->options, ' ','no');
          $this->options = get_option('_GestBlog_settings');
        }
        $defaut['GestBlog'] = 'true';
        $defaut['maintenance'] = '';
        $defaut['activate'] = '';
        $defaut['hotlink'] = '';
        $defaut['image'] = '';
        $defaut['domaine'] = '';
        $defaut['hackers'] = '';
        $defaut['exp_reg'] = '';
        $defaut['raz_cejour'] = date('d-m-Y');
        $defaut['robot'] = '';
        $defaut['intrusion'] = '3';
        $defaut['avert']  = '0';
        $defaut['message_view'] = '';
        foreach ($defaut as $key=>$valeur) {
          if ( empty($this->options[$key] ) ) $this->options[$key] = $defaut[$key];
        }
        add_option('_GestBlog_settings', $this->options, ' ','no');
        // Table 301
        $this->structure_301 = '` ('
          . ' `id` int(11) NOT NULL auto_increment,'
          . ' `tag` text NOT NULL,'
          . ' `code` int(11) NOT NULL,'
          . ' `lien` text NOT NULL,'
          . ' `stat` int(11) NOT NULL,'
          . ' `ip` text NOT NULL,'
          . ' KEY `id` (`id`),'
          . ' bot int(11) NOT NULL'
          . ' ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 PACK_KEYS=1 CHECKSUM=1 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1'; 
        $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_301. $this->structure_301;
        $wpdb->query($sql);
        $sql = "SHOW COLUMNS FROM ".$this->tb_301." LIKE 'ip'";
        $result = $wpdb->query($sql);
        if ( empty($result) ) {
          $sql = "ALTER TABLE ".$this->tb_301." ADD `ip` TEXT NOT NULL AFTER `stat`"; 
          $wpdb->query($sql);
        }   
        //Table iplist
        $this->structure_iplist = '` ('
          . ' `id` int(11) NOT NULL auto_increment,'
          . ' `ip` text NOT NULL,'
          . ' `keyword` text NOT NULL,'
          . ' `count` int(11) NOT NULL default \'0\','
          . ' `passage` date NOT NULL,'
          . ' PRIMARY KEY  (`id`)'
          . ') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_iplist. $this->structure_iplist;
        $wpdb->query($sql);
        $sql = "SHOW COLUMNS FROM ".$this->tb_iplist." LIKE 'passage'";
        $result = $wpdb->query($sql);
        if ( empty($result) ) {
          $sql = "ALTER TABLE ".$this->tb_iplist." ADD `passage` DATE NOT NULL AFTER `count`"; 
          $wpdb->query($sql);
        }   
        //Table Robot
        $this->structure_robot = '` ('
          . ' `id` int(11) NOT NULL auto_increment,'
          . ' `nom` text NOT NULL,'
          . ' `d` text NOT NULL,'
          . ' `a` text NOT NULL,'
          . ' PRIMARY KEY  (`id`)'
          . ') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_robot. $this->structure_robot;
        $wpdb->query($sql);

        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'panel-my-blog', false, $plugin_dir );
      }
      /* Fin du constructeur */
      //
      /* Ajout du ou des menus */
      function gestblog_menu() {
        add_menu_page('Panel My Blog', 'Panel My Blog', 10, $this->dir, '');
        add_submenu_page($this->dir,'Redirection Management', __("Redirect 301","panel-my-blog"), 10, $this->dir,array($this,'gestblog404'));
        add_submenu_page($this->dir,'Blacklist IP Management', __("Blacklist","panel-my-blog"), 10, 'ip-blaclist',array($this,'gestblogblacklist'));
        add_submenu_page($this->dir,'Robots.txt', __("BOT Options","panel-my-blog"), 10, 'bot',array($this,'bot'));
        add_submenu_page($this->dir,'Plugin Options', __("Options","panel-my-blog"), 10, 'option-maintenance',array($this,'gestblogoptions'));
        add_submenu_page($this->dir,'Help Panel-My-Blog', __("Help","panel-my-blog"), 10, 'aide',array($this,'aide'));
      }
      /* Menu Redirect 301 */
      function gestblog404() {
        ?>
        <div class="gestblog"><img src="<?php echo $this->url; ?>wp-content/plugins/panel-my-blog/panel_my_blog.png" title="PANEL-MY-BLOG - Version: <?php echo $this->version; ?>"><sup>Version: <?php echo $this->version; ?></sup><br />
        <div style="text-align: right;"><a name="haut"></a><a href="#bas"><?php _e("Down page","panel-my-blog"); ?></a>&nbsp;&nbsp;</div>
        <?php
        PMB_not_found();
        global $wpdb;
        // Modification ou enregistrement des différentes rows
        if ( isset($_POST['modif']) ) {
        	$sql = $wpdb->get_results ("SELECT id, tag, code, lien, bot FROM `".$this->tb_301."` ORDER BY tag ASC");
          foreach ($sql as $valeur) {
            if ( $_POST['r-'.$valeur->id] == "X" ) {
        			$wpdb->query("DELETE FROM `".$this->tb_301."` WHERE `id` = $valeur->id");
            } elseif ( $_POST['r-'.$valeur->id] == "M" ) {
              $onbot = isset($_POST['bot-'.$valeur->id]);
              if ( isset($_POST['bot-'.$valeur->id] )) {
          			$wpdb->query("UPDATE `".$this->tb_301."` SET `tag`='".$_POST['tag-'.$valeur->id]."', `code`= 410, `lien`='', `bot`='".$onbot."' WHERE `id` = '".$valeur->id."'");
              } else {
           			$wpdb->query("UPDATE `".$this->tb_301."` SET `tag`='".$_POST['tag-'.$valeur->id]."', `code`= 301, `lien`='".$_POST['lien-'.$valeur->id]."', `bot`='".$onbot."' WHERE `id` = '".$valeur->id."'");
              }
            }
          } ?>
          <br /><span class="successfull"><?php _e("Change successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif (isset($_POST['raz']) ) {
        	$wpdb->query("UPDATE `".$this->tb_301."` SET `stat` = '0'");
          $this->options = get_option('_GestBlog_settings');
          $this->options['raz_cejour'] = date('d-m-Y');
          update_option('_GestBlog_settings', $this->options, ' ','no');
          ?>
          <br /><span class="successfull"><?php _e("Reinit counters  successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif (isset($_POST['zip'])) {
          $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_301_new. $this->structure_301;
          $wpdb->query($sql);
        	$sql = $wpdb->get_results("SELECT tag, code, lien, stat, bot FROM `".$this->tb_301."` ORDER BY tag ASC");
          foreach ($sql as $valeur) {
            $wpdb->query("INSERT INTO `".$this->tb_301_new."` (`id`, `tag`, `code`, `lien`, `stat`, `bot`) VALUES ('', '".$valeur->tag."', '".$valeur->code."', '".$valeur->lien."', '".$valeur->stat."', '".$valeur->bot."')");
          }
          $sql = "DROP TABLE `".$this->tb_301."`"; 
          $wpdb->query($sql);
          $sql = "ALTER TABLE `".$this->tb_301_new."` RENAME `".$this->tb_301."`"; 
          $wpdb->query($sql);
          ?>
          <br /><span class="successfull"><?php _e("Compact table successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif (isset($_POST['vider'])) {
          $sql = "DROP TABLE `".$this->tb_301."`";
          $wpdb->query($sql);
          ?>
          <br /><span class="successfull"><?php _e("The table was cleaned successfully","panel-my-blog"); ?></span><br />
          <?php
        }
        // Affiche de la page Redirect 301 de base
        $this->options = get_option('_GestBlog_settings');
        $visite = 0;
        $sql = $wpdb->get_results("SELECT id, tag, code, lien, stat, ip, bot FROM `".$this->tb_301."` ORDER BY id ASC");
      	?>
        <br /><br />
        <form method="post" action="">
        <div class="mon-contenu">        
        <?php
        if ( count($sql) == 0 ) {
          ?>
          <p style="text-align: center"><strong><?php _e("Congratulations !!!","panel-my-blog"); ?><br />
          <?php _e("There are currently no broken links on your blog","panel-my-blog"); ?></strong><br /><br />
          <?php
        } else {
         	 ?>
           <div class="tableau">            
           <?php
          foreach ($sql as $valeur) {
            $largeur="50px";
            ?>
            <p> 
            <span class="ColD">
              <a class="info" href="#"><?php _e("D","panel-my-blog"); ?><span><?php _e("Use to delete entry","panel-my-blog"); ?></span></a>
              <input type="radio" name="r-<?php echo $valeur->id; ?>" value="X" />
            </span> 
            <span class="ColM">
              <a class="info" href="#"><?php _e("M","panel-my-blog"); ?><span><?php _e("Use to apply modification","panel-my-blog"); ?></span></a>
              <input type="radio" name="r-<?php echo $valeur->id; ?>" value="M" checked />
            </span>
            <span class="ColN">
              <a class="info" href="#"><?php _e("ID","panel-my-blog"); ?><span><?php _e("ID of the entry","panel-my-blog"); ?></span></a>
              <?php echo $valeur->id; ?>
            </span>
            <span class="ColTag">
              <a class="info" href="#"><?php _e("TAG","panel-my-blog"); ?><span><?php _e("Query input","panel-my-blog"); ?></span></a>
              <input size="<?php echo $largeur;?>" type="text" name="tag-<?php echo $valeur->id; ?>" value="<?php echo $valeur->tag; ?>" />
            </span>
            <span class="ColCode">
              <a class="info" href="#"><?php _e("CODE","panel-my-blog"); ?><span><?php _e("Error code","panel-my-blog"); ?></span></a>
              <?php echo $valeur->code; ?>
            </span>
            <?php
            if ( $valeur->code == 404 ) {
              $tableau = listing($valeur->tag,$this->url);
              ?>
              <span class="ColLink">
                <small style="color:green"><?php _e("Select in a dropdown list of keywords or related articles","panel-my-blog"); ?></small><br />
                <select id="men_der-<?php echo $valeur->id; ?>" style="width:350px;size:1" ><?php echo $tableau; ?></select><br />
                <small style="color:green"><?php _e("If not manually enter an existing link","panel-my-blog"); ?></small><br />
                <input size="<?php echo $largeur;?>" type="text" name="lien-<?php echo $valeur->id; ?>" id="lien-<?php echo $valeur->id; ?>" value="<?php echo $this->url; ?>" size="40px" /><br />
                <input size="<?php echo $largeur;?>" type="text" name="ip-<?php echo $valeur->id; ?>" value="<?php echo $valeur->ip; ?>" disabled />@IP 
              </span>  
            <?php
            }  elseif ( $valeur->code == 410 ) {
              ?>
              <span class="ColLink">
                <a class="info" href="#"><?php _e("LINK","panel-my-blog"); ?><span><?php _e("Link to replace (vacuum creates the error 410)","panel-my-blog"); ?></span></a>
                <input size="<?php echo $largeur;?>" style="color:grey" type="text" name="lien-<?php echo $valeur->id; ?>" value="<?php _e("The ressource is unavailable: no redirection","panel-my-blog"); ?>" disabled />
              </span>
              <?php
            } else {
              ?>
              <span class="ColLink">
                <a class="info" href="#"><?php _e("LINK","panel-my-blog"); ?><span><?php _e("Link to replace (vacuum creates the error 410)","panel-my-blog"); ?></span></a>
                <input size="<?php echo $largeur;?>" type="text" name="lien-<?php echo $valeur->id; ?>" value="<?php echo $valeur->lien; ?>" />
              </span>
              <?php
            }
            ?>
            <span class="ColNb">
              <a class="info" href="#"><?php _e("COUNT","panel-my-blog"); ?><span><?php _e("Query count","panel-my-blog"); ?></span></a>
              <?php echo $valeur->stat; ?>
            </span>
            <?php
            if ($valeur->bot == 0) { ?>
              <span class="ColBot">
                <a class="info" href="#"><?php _e("BOT","panel-my-blog"); ?><span><?php _e("Blocking by include in virtual robots.txt","panel-my-blog"); ?></span></a>
                <input type="checkbox" name="bot-<?php echo $valeur->id; ?>" />
              </span>
            <?php
            } else {
            ?>
              <span class="ColBot">
                <a class="info" href="#"><?php _e("BOT","panel-my-blog"); ?><span><?php _e("Blocking by include in virtual robots.txt","panel-my-blog"); ?></span></a>
                <input type="checkbox" name="bot-<?php echo $valeur->id; ?>" value="1" checked />
              </span>
            <?php
            }
            $visite += $valeur->stat;
            ?>
            </p>   
            <p><hr /></p>
            <?php
          }
        }
        ?>
        </div>
        </div>
        <?php
        if ( count($sql) != 0 ) {
          ?>
          <p style="text-align: center; font-weight: bold; color: green; font-size: 16px"><?php echo $visite; _e(" visits retrieved dynamically from ","panel-my-blog"); echo $this->options['raz_cejour']; ?></p>
          <?php
        }
        ?>
        <p style="text-align: center">
        <input class="button-primary" type="submit" name="modif" value="<?php _e("Save changes...","panel-my-blog"); ?>" />&nbsp;&nbsp;
        <input class="button-primary" type="submit" name="zip" value="<?php _e("Compact table","panel-my-blog"); ?>" />&nbsp;&nbsp;
        <input class="button-primary" type="submit" name="raz" value="<?php _e("Reinit counters","panel-my-blog"); ?>" />&nbsp;&nbsp;
        <input class="button-primary" type="submit" name="vider" value="<?php _e("Erase all entries","panel-my-blog"); ?>" />&nbsp;&nbsp;
        </p></form><br />
        <?php // script de gestion des listes déroulantes ?>
        <script type="text/javascript">
        jQuery(function(){
          jQuery( 'select[id^="men_der-"]' ).on( "change", function( event ) {
            var list_cur=jQuery(this);
            var men_der_id=list_cur.attr("id");
            men_der_id=men_der_id.split("-")[1];
            var men_der=list_cur.val();
            if ( men_der == "" ) men_der="<?php echo $this->url; ?>";
            jQuery('input[id="lien-'+men_der_id+'"]').val(men_der);
          });
        });
        </script>
        <em><?php _e("This plugin was created by","panel-my-blog"); ?> Bernard G. - <a href="http://www.bernard-g.fr" target="_blank">Bernard G. Photographie</a></em><br />
        </div>
        <div style="text-align:right;"><a name="bas"></a><a href="#haut"><?php _e("Up page","panel-my-blog"); ?></a>&nbsp;&nbsp;</div>
        <?php
      }
      
      /* Menu blacklist */  
      function gestblogblacklist() {
        ?>
        <div class="gestblog"><img src="<?php echo $this->url; ?>wp-content/plugins/panel-my-blog/panel_my_blog.png" title="PANEL-MY-BLOG - Version: <?php echo $this->version; ?>"><sup>Version: <?php echo $this->version; ?></sup><br />
        <?php
        PMB_not_found();
        global $wpdb;
        if (isset($_POST['maj_blacklist'])) { // mise à jour de la blacklist
        	$sql = $wpdb->get_results ("SELECT id, ip, count FROM `".$this->tb_iplist."`");
          $this->options = get_option('_GestBlog_settings');
        	if  ( empty($sql) ) { 
            $this->options['blacklist'] = "0";
          } else {
            $this->options['blacklist'] = "1";
          }
          $this->options['avert']  = '0';
          update_option('_GestBlog_settings', $this->options, ' ','no');
          foreach ($sql as $valeur) {
            if ( $_POST['r-'.$valeur->id] == "X" ) {
        			$wpdb->query("DELETE FROM `".$this->tb_iplist."` WHERE `id` = $valeur->id");
            }
          }
          if ( $_POST['ip-'.($valeur->id + 1)] != "" ) {
            $wpdb->query( $wpdb->prepare(
              "INSERT INTO `".$this->tb_iplist."` (`id`, `ip`, `keyword`, `count`, `passage`) VALUES ('', '%s', '%s', '0', '%s')",
                array($_POST['ip-'.($valeur->id + 1)], __('Manual entry','panel-my-blog'),date('Y-m-d'))));
          }
          if ( $this->options['activate'] == "on" ) {
            include_once("activate.php");
            $automatic = new admin_activate();
            $automatic->maj_access();
          }
          unset($_POST['maj_blacklist']);
          ?>
          <br /><span class="successfull"><?php _e("Change successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif (isset($_POST['zip_blacklist'])) {
          $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_iplist_new. $this->structure_iplist;
          $wpdb->query($sql);
        	$sql = $wpdb->get_results("SELECT * FROM `".$this->tb_iplist."`ORDER BY passage ASC");
          foreach ($sql as $valeur) {
            $wpdb->query("INSERT INTO `".$this->tb_iplist_new."` (`id`, `ip`, `keyword`, `count`, `passage`) VALUES ('', '".$valeur->ip."', '".$valeur->keyword."', '".$valeur->count."', '".$valeur->passage."')");
          }
          $sql = "DROP TABLE `".$this->tb_iplist."`"; 
          $wpdb->query($sql);
          $sql = "ALTER TABLE `".$this->tb_iplist_new."` RENAME `".$this->tb_iplist."`"; 
          $wpdb->query($sql);
          ?>
          <br /><span class="successfull"><?php _e("Compact table successfully","panel-my-blog"); ?></span><br />
          <?php
        }
        $sql = $wpdb->get_results("SELECT * FROM `".$this->tb_iplist."`");
        ?>
        <br /><form method="post" action=""><br />
        <?php
        if ( $this->options['activate'] == "on" ) {
          ?>
          <span class="attention"><? _e("LIST OF IP ADDRESS BLACKLISTED *** WARNING: File HTACCESS will be regenerated ***","panel-my-blog"); ?></span><br /><br />
          <?php
        } else {
          ?>
          <span class="attention"><?php _e("LIST OF IP ADDRESS BLACKLISTED *** File HTACCESS desactived ***","panel-my-blog"); ?></span><br /><br />
          <?php
        }
        ?>
        <em><? _e("My IP address is currently :","panel-my-blog"); echo $_SERVER["REMOTE_ADDR"]; ?></em><br /><br />
        <div class="mon-contenu">
        <?php
        if ( count($sql) == 0 ) {
          ?>
          <p style="text-align: center"><strong><?php _e("There is nothing IP address blacklisted !!!","panel-my-blog"); ?></strong></p><br /><br />
          <?php
        } else {
         	 ?>
           <div class="tableau">        
           <?php
        }
        
        foreach ($sql as $valeur) {
          ?>
          <p>
          <span class="ColD">
            <a class="info" href="#"><?php _e("D","panel-my-blog"); ?><span><?php _e("Use to delete entry","panel-my-blog"); ?></span></a>
            <input type="radio" name="r-<?php echo $valeur->id; ?>" value="X" />
          </span>
          <span class="ColM">
            <a class="info" href="#"><?php _e("M","panel-my-blog"); ?><span><?php _e("Use to apply modification","panel-my-blog"); ?></span></a>
            <input type="radio" name="r-<?php echo $valeur->id; ?>" value="M" checked />
          </span>
          <span class="ColN">
          <a class="info" href="#"><?php _e("ID","panel-my-blog"); ?><span><?php _e("ID of the entry","panel-my-blog"); ?></span></a>
            <?php echo $valeur->id; ?>
          </span>
          <span class="ColIP">
            <a class="info" href="#"><?php _e("IP","panel-my-blog"); ?><span><?php _e("Address IP query input","panel-my-blog"); ?></span></a>
            <input type="button" value="<?php echo $valeur->ip; ?>" 
              onclick="javascript:window.open('http://whatismyipaddress.com/ip/<?php echo $valeur->ip; ?>#Geolocation-Map','','dialog=no, location=no, menubar=no, status=no, scrollbars=no, resizable=no, toolbar=no, top=100, left=100, width=400, Height=400')" size="20" >
          </span>
          <span class="ColKey">
            <a class="info" href="#"><?php _e("KEYWORD","panel-my-blog"); ?><span><?php _e("Query input","panel-my-blog"); ?></span></a>&nbsp;
            <input type="text" name="keyword-<?php echo $valeur->keyword; ?>" value="<?php echo $valeur->keyword; ?>" size="15" disabled />
          </span>
          <span class="ColNb">
            <a class="info" href="#"><?php _e("COUNT","panel-my-blog"); ?><span><?php _e("Query count","panel-my-blog"); ?></span></a>
            <?php echo $valeur->count; ?>
          </span>
          <span class="ColDate">
          <a class="info" href="#"><?php _e("DATE","panel-my-blog"); ?><span><?php _e("Date","panel-my-blog"); ?></span></a>
            <input type="text" name="passage-<?php echo $valeur->id; ?>" value="<?php echo $valeur->passage; ?>" size="9" disabled />
          </span>
          </p>
          <p><hr></p>
          <?php
        }
        ?>
        </div>
        <div style="text-align:center">
        <span><?php _e("Add the following to block an IP address","panel-my-blog"); ?></span><br />
        <input type="text" name="ip-<?php echo $valeur->id + 1; ?>" value="" size="20" />
        </div><br /></div><br />
        <p style="text-align: center">
        <input class="button-primary" type="submit" name="maj_blacklist" value="<?php _e("Save changes...","panel-my-blog"); ?>" />
        <input class="button-primary" type="submit" name="zip_blacklist" value="<?php _e("Compact table","panel-my-blog"); ?>" />
        </p>
        </form><br />
        <em><?php _e("This plugin was created by","panel-my-blog"); ?> Bernard G. - <a href="http://www.bernard-g.fr" target="_blank">Bernard G. Photographie</a></em><br />
        <?php
      }
      
      /* Menu robots.txt */
      function bot() {
        ?>
        <div class="gestblog"><img src="<?php echo $this->url; ?>wp-content/plugins/panel-my-blog/panel_my_blog.png" title="PANEL-MY-BLOG - Version: <?php echo $this->version; ?>"><sup>Version: <?php echo $this->version; ?></sup><br />
        <?php
        PMB_not_found();
        global $wpdb;
        include_once("activate.php");
        //$this->options = get_option('_GestBlog_settings');
        if ( isset($_POST['modif']) ) { // enregistrement des options
          //$this->options['message_view'] =stripslashes($_POST['message_view']); 
          //update_option('_GestBlog_settings', $this->options, ' ','no');
          //$this->options = get_option('_GestBlog_settings');
          // gestion des enregistrements du ROBOT.TXT
          $wpdb->query("TRUNCATE TABLE `".$this->tb_robot."`");
          for ( $id=0; $id <= $_POST['total']; $id++ ) {
            if ( $_POST['nom-'.$id] != "") { 
              $wpdb->query( $wpdb->prepare(  
                "INSERT INTO `".$this->tb_robot."` (`id`, `nom`, `d`, `a`) VALUES ('', '%s', '%s', '%s')",
                  array($_POST['nom-'.$id],$_POST['d-'.$id],$_POST['a-'.$id])
                ) );
            }
          }
          ?>
          <br /><span class="successfull"><?php _e("Change successfully","panel-my-blog"); ?></span><br />
          <?php
        }
        // Chargement du formulaire
        ?>
        <br /><form method="post" action=""><br />
        <?php
        // Gestion du robots.txt
        ?>
    	  <strong><?php _e("VIRTUAL ROBOTS.TXT: Managment directories or files for access denied to bots","panel-my-blog"); ?></strong><br />
    	  <em><?php _e("WARNING: this file contains the virtual links 410 error","panel-my-blog"); ?></em><br /><br />
    	  <?Php $sql = $wpdb->get_results("SELECT id, nom, d, a FROM `".$this->tb_robot."`"); ?>
        <div class="mon-contenu">
          <div class="tableau">
          <?php foreach ($sql as $valeur) { ?>
            <p>
              <span class="ColRobot">
                <?php _e("Bot","panel-my-blog"); ?>&nbsp;<input type="text" name="nom-<?php echo $valeur->id; ?>" value="<?php echo $valeur->nom; ?>" size="20" />
              </span>
              <span class="ColDis">
                <?php _e("Disallow","panel-my-blog"); ?>&nbsp;<input type="text" name="d-<?php echo $valeur->id; ?>" value="<?php echo $valeur->d; ?>" size="40" />
              </span>
              <span class="ColAll">
                <?php _e("Allow","panel-my-blog"); ?>&nbsp;<input type="text" name="a-<?php echo $valeur->id; ?>" value="<?php echo $valeur->a; ?>" size="40" />
              </span>
            </p>
              <?php
              $total = $valeur->id +1;
            }
            if ( $valeur->id == 0 ) $total = 0;
            ?>
          <p>
          <span class="ColRobot">
            <?php _e("Bot","panel-my-blog"); ?>&nbsp;<input size="20" type="text" name="nom-<?php echo $total; ?>" value="" />
          </span>         
          <span class="ColDis">
            <?php _e("Disallow","panel-my-blog"); ?>&nbsp;<input size="40" type="text" name="d-<?php echo $total; ?>" value="" />
          </span>
          <span class="ColAll">
            <?php _e("Allow","panel-my-blog"); ?>&nbsp;<input size="40" type="text" name="a-<?php echo $total; ?>" value="" />
          </span>
          </p>
          </div>
        </div>
        <br /> <br />
        <input type="hidden" name="total" value="<?php echo $total; ?>" />
        <div style="text-align:center">
        <?php
        if(is_file($_SERVER['DOCUMENT_ROOT']."/robots.txt")) { ?>
          <strong>
          <?php _e("REMOVE ROBOTS.TXT FILE PRESENT THE ROOT OF YOUR WEBSITE TO USE VIRTUAL ROBOTS.TXT","panel-my-blog"); ?>
          </strong>
        <?php } else { ?>
          <a href="<?php echo $this->url; ?>robots.txt" target="_blank"><?php _e("VIEW VIRTUAL FILE ROBOTS.TXT","panel-my-blog"); ?></a><br />
        <?php } ?>
        <br />
        <input class="button-primary" type="submit" name="modif" value="<?php _e("Save changes..","panel-my-blog"); ?>" /><br /><br />
        </div>
        </form>
        <em><?php _e("This plugin was created by","panel-my-blog"); ?> Bernard G. - <a href="http://www.bernard-g.fr" target="_blank">Bernard G. Photographie</a></em><br />
        </div>
        <?php
      }
        
      /* Menu Options */
      function gestblogoptions() {
        ?>
        <div class="gestblog"><img src="<?php echo $this->url; ?>wp-content/plugins/panel-my-blog/panel_my_blog.png" title="PANEL-MY-BLOG - Version: <?php echo $this->version; ?>"><sup>Version: <?php echo $this->version; ?></sup><br />
        <?php
        PMB_not_found();
        global $wpdb;
        include_once("activate.php");
        $this->options = get_option('_GestBlog_settings');
        if ( isset($_POST['modif']) ) { // enregistrement des options
          $this->options['maintenance'] = $_POST['maintenance'];
          $this->options['activate'] = $_POST['activate'];
          $this->options['hotlink'] = $_POST['hotlinking'];
          $this->options['image'] = $_POST['image'];
          $this->options['domaine'] = $_POST['domaine'];
          //$this->options['robot'] = $tableau;
          $this->options['hackers'] = stripslashes($_POST['hackers']);
          $this->options['exp_reg'] = ""; 
          $this->options['intrusion']  = $_POST['intruse'];
          $this->options['message_view'] =stripslashes($_POST['message_view']); 
          update_option('_GestBlog_settings', $this->options, ' ','no');
          $this->options = get_option('_GestBlog_settings');
          ?>
          <br /><span class="successfull"><?php _e("Change successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif ( isset($_POST['avis']) ) { // Suggestion query anti-hackers
          $this->options['hackers'] .= "\n+Result:+\nIsResolved\n/\'\nsafe_mode\n/data:image/\n/adimages\nClone\ndefender\nantibot\nRK=0\n/administrator/\n/reg.asp\n/bcloud/\nfckeditor\nClone";
        } elseif ( isset($_POST['genere']) ) { // génère le HTACCESS
          $automatic = new admin_activate();
          $automatic->maj_access();
          unset($_POST['genere']);
          ?>
          <br /><span class="successfull"><?php _e("File HTACCESS generated successfully","panel-my-blog"); ?></span><br />
          <?php
        } elseif ( isset($_POST['preview']) ) { // visualiser le HTACCESS
          $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/.htaccess");
          ?>
          <strong><?php _e("View content of HTACCESS file","panel-my-blog"); ?></strong><br />
          <textarea name="vide" rows="24" cols="100"><?php echo $data; ?></textarea><br />
          <?php 
        }
        // Chargement du formulaire
        ?>
        <br /><form method="post" action=""><br />
        <?php // Active l'utilisation du HTACCESS ?>
        <p class="attention"><strong><?php _e( "Activate using file HTACCESS","panel-my-blog" ); ?></strong><br />
        <?php
        if ( $this->options['activate'] == "on" ) {
          ?> <input type="checkbox" name="activate" checked /> OK <?php
        } else {
          ?> <input type="checkbox" name="activate" /> OK <?php
        }
        ?> </p><br /> <?php
        // Active ou désactive les éléments du formulaire
        ?>
        <strong><?php _e("Management of maintenance mode (validated by the HTACCESS)","panel-my-blog"); ?></strong><br />
        <?php
        if ( $this->options['maintenance'] == "on" ) {
          ?> <input type="checkbox" name="maintenance" checked />&nbsp;&nbsp;Activation<br /><br /><em> <?php
        } else {
          ?> <input type="checkbox" name="maintenance" />&nbsp;&nbsp;Activation<br /><br /><em> <?php
        }
        _e("Enter custom text of maintenance","panel-my-blog"); 
        ?>
        </em><br />
        <textarea name="message_view"  rows="6" cols="50"><?php echo $this->options['message_view']; ?></textarea><br />
        <br /><br />
    	  <strong><?php _e("Management of sites allowed to hotlinking","panel-my-blog"); ?></strong><br />
        <textarea name="hotlinking" rows="6" cols="50"><?php echo $this->options['hotlink']; ?></textarea><br />
        <?php
        if ( $this->options['image'] == "on" ) {
          ?> <input type="checkbox" name="image" checked /> Image de remplacement <?php
        } else {
          ?> <input type="checkbox" name="image" /> Image de remplacement <?php
        }
        ?>
        <br /><br />
    	  <strong><?php _e("Deny access to visitors from...","panel-my-blog"); ?></strong><br />
        <textarea name="domaine" rows="6" cols="50" ><?php echo $this->options['domaine']; ?></textarea><br />
        <br /><br />
    	  <strong><?php _e("Managment keywords against hackers","panel-my-blog"); ?></strong><br />
        <textarea name="hackers" rows="6" cols="50" ><?php echo $this->options['hackers']; ?></textarea><br />
        <input class="button-primary" type="submit" name="avis" value="<?php _e("Suggest...","panel-my-blog"); ?>" /><br />
        <br /><br /> <?php
        // Nombre maximum de requêtes ERROR 404
    	  ?>
        <p><?php _e("Maximum number of queries error 404 before IP blacklisting","panel-my-blog"); ?>&nbsp;&nbsp;
    	  <select name="intruse">
    	  <?php
    	  echo ($this->options['intrusion'] == 999) ? '<option value="999" selected>'.__("Desactived","panel-my-blog").'</option>' :'<option value="999">'.__("Desactived","panel-my-blog").'</option>';         
    	  echo ($this->options['intrusion'] == 1) ? '<option value="1" selected> 1</option>' : '<option value="1"> 1</option>';         
    	  echo ($this->options['intrusion'] == 2) ? '<option value="2" selected> 2</option>' : '<option value="2"> 2</option>';         
    	  echo ($this->options['intrusion'] == 3) ? '<option value="3" selected> 3</option>' : '<option value="3"> 3</option>';         
    	  echo ($this->options['intrusion'] == 4) ? '<option value="4" selected> 4</option>' : '<option value="4"> 4</option>';         
    	  echo ($this->options['intrusion'] == 5) ? '<option value="5" selected> 5</option>' : '<option value="5"> 5</option>';         
    	  echo ($this->options['intrusion'] == 6) ? '<option value="6" selected> 6</option>' : '<option value="6"> 6</option>';         
    	  ?>
        </select></p><br />
        <?php
        // Test de l'utilisation du HTACCESS
        if ( $this->options['activate'] == "on" ) $visible = ""; else $visible = "disabled";
        ?>
        <br /><input class="button-primary" type="submit" name="modif" value="<?php _e("Save changes...","panel-my-blog"); ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input class="button-primary" type="submit" name="genere" value="<?php _e("Generate file HTACCESS","panel-my-blog"); ?>" <?php echo $visible; ?> />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input class="button-primary" type="submit" name="preview" value="<?php _e("View Htaccess","panel-my-blog"); ?>" />
        <br /><br />
        </form><br />
        <em><?php _e("This plugin was created by","panel-my-blog"); ?> Bernard G. - <a href="http://www.bernard-g.fr" target="_blank">Bernard G. Photographie</a></em><br />
        </div>
        <?php
      }

      /* Menu d'aide */
      function aide() {
        if ( WPLANG == "fr_FR") {
          include_once "aide.inc";
        } else {
          include_once "help.inc";
        }
      }
      
      /* Style CSS */
      function gestblog_init() {
        wp_enqueue_style( 'gestblogStyle', plugins_url('style.css', __FILE__) );
      }
    }
  }
  $my_admin_page = new GestBlog();
  add_action('admin_init', array($my_admin_page, 'gestblog_init'));
  add_action('admin_menu', array($my_admin_page, 'gestblog_menu'),1);
}

/* Gestion des liens rompus */
function gestion_301($erreur, $visiteur) {
  $visiteur= strtolower($visiteur);
  $ip = $_SERVER["REMOTE_ADDR"];  
  $champ = explode("?", $erreur);
  if ( !empty($champ) ) $erreur = $champ[0];
  global $wpdb;
  $table_301 = $wpdb->prefix . 'gestblog_301';
  $table_iplist = $wpdb->prefix . 'gestblog_iplist';
  // recherche de l'enregistrement de l'erreur
  $sql = $wpdb->get_results("SELECT * FROM `".$table_301."` WHERE `tag` = '".$erreur."'");
  if ( empty($sql) ) {
    // Enregistrement de l'erreur 301 parce qu'inexistante
    $wpdb->query( $wpdb->prepare(
      "INSERT INTO `".$table_301."` (`id`, `tag`, `code`, `lien`, `stat`, `ip`) VALUES ('', '%s', '%s', '', '0', '".$ip."')",
        array($erreur,"404")
      ) );
    $options = get_option('_GestBlog_settings');
    // gestion du blacklisting si le mode activate sur on
    if ($options['activate'] == "on") {
      // Test de l'adresse IP à bannir afin d'éviter les robots
      $bot = false;
      $spiders = file(dirname( __FILE__ )."/bot.txt");
      foreach ($spiders as $line_num => $key) {
        if ( stristr($visiteur, trim($key)) !== false ) {
          $bot = true;
          break;
        }
      }
      unset($spiders);
      if ( $bot == false) {
        // Chargement de la table des mots à bloquer
        if ($options['hackers'] != '') {
          $keyword = explode(chr(13).chr(10),$options['hackers']);
          foreach ($keyword as $valeur) {
            if ( stristr($erreur,$valeur) ) {
              $sql=$wpdb->get_results("SELECT * FROM `".$table_iplist."` WHERE `ip` = '".$ip."'");
              if ( empty($sql) ) {
                $wpdb->query( $wpdb->prepare(
                  "INSERT INTO `".$table_iplist."` (`id`, `ip`, `keyword`, `count`, `passage`) VALUES ('', '%s', '%s', '1', '%s')",
                    array($ip,$valeur,date('Y-m-d'))
                  ) );
              } else {
                $wpdb->query("UPDATE `".$table_iplist."` SET count=count+1 where `ip` = '".$sql->ip."'");
              }
              // Automatic blacklisting according to the definite key words
              include_once("activate.php");
              $automatic = new admin_activate();
              $automatic->maj_access(TRUE);
              $options['avert']  = '1';
              update_option('_GestBlog_settings', $options, ' ','no');
              
            }
          }
        }
        //Test d'intrusion par plus de trois URL code 404 pour la même IP
        $sql404 = $wpdb->get_results("SELECT * FROM `".$table_301."` where `code` = '404' and `ip` <> '' ORDER BY 'ip'");
        if ( !empty($sql404) ) {
          foreach ($sql404 as $ipvaleur) {
            $sqlcount =  $wpdb->get_results("SELECT * FROM `".$table_301."` where `code` = '404' and `ip` = '".$ipvaleur->ip."'");
            if (count($sqlcount) >=  $options['intrusion']) {
              $sqlblk = $wpdb->get_results("SELECT * FROM `".$table_iplist."` WHERE `ip` = '".$ipvaleur->ip."'");
              if ( empty($sqlblk) ) {
                $wpdb->query( $wpdb->prepare(
                  "INSERT INTO `".$table_iplist."` (`id`, `ip`, `keyword`, `count`, `passage`) VALUES ('', '%s', 'Intrusion', '1', '%s')",
                    array($ipvaleur->ip,date('Y-m-d'))
                  ) );
              } else {
                $wpdb->query("UPDATE `".$table_iplist."` SET count=count+1 where `ip` = '".$ipvaleur->ip."'");
              }
              // Automatic blacklisting according to the definite key words
              include_once("activate.php");
              $automatic = new admin_activate();
              $automatic->maj_access(TRUE);
              $options['avert']  = '1';
              update_option('_GestBlog_settings', $options, ' ','no');
            } 
          }      
        }
      }
    }
  } else {
    foreach ($sql as $valeur) {
      if ( $valeur->code == 301 ) {
        $wpdb->query("UPDATE `".$table_301."` SET stat=stat+1 WHERE `tag`= '".$erreur."'");
  		  wp_redirect( $valeur->lien, 301 );
        exit();
      } elseif ( $valeur->code == 404 ) {
        $wpdb->query("UPDATE `".$table_301."` SET stat=stat+1 WHERE `tag`= '".$erreur."'");
      } elseif ( $valeur->code == 410 ) {
        $wpdb->query("UPDATE `".$table_301."` SET stat=stat+1 WHERE `tag`= '".$erreur."'");
        $site = get_site_url();
        header("HTTP/1.1 410 Gone");
        //echo '<META HTTP-EQUIV="Refresh" CONTENT="30; URL='.$site.'">';
        echo "</head>\n<body>\n</body>\n</html>\n";
        exit();
      }
		}
  }
  $wpdb->flush();
}

/* Ajout de l'entête de test erreur 404 */
function meta_panel_my_blog() {
  if ( is_404() ) {
    gestion_301($_SERVER['REQUEST_URI'],$_SERVER['HTTP_USER_AGENT']);
  }
}
add_action('wp_head', 'meta_panel_my_blog',1);

/* Gestion du fichier robots virtuel */
function my_bots($output) {
  global $wpdb;
  $options = get_option('_GestBlog_settings');
	$site_url = parse_url( site_url() );
	$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
	$public = get_option( 'blog_public' );
	if ( '0' == $public ) {
		$output = "User-agent: *\n";
    $output .= "Disallow: $path\n";
  } else {
    $output = "User-agent: *\n";
    $output .= "Disallow: $path/wp-admin\n";
    $output .= "Disallow: $path/wp-includes\n";
    $table_robot = $wpdb->prefix . 'gestblog_robot';
    $sql = $wpdb->get_results("SELECT id, nom, d, a FROM `".$table_robot."`");
    $old="";
    foreach ($sql as $valeur) {
      if ($valeur->nom != "") {
        if ( $valeur->nom != $old ) {
          $output .= "User-agent: ".$valeur->nom."\n";
        }
        if ($valeur->d != "" ) $output .= "Disallow: $path$valeur->d\n";
        if ($valeur->a != "" ) $output .= "Allow: $path$valeur->a\n";
        $old=$valeur->nom;
      }
		}
    $table_301 = $wpdb->prefix . 'gestblog_301';
    $sql = $wpdb->get_results("SELECT tag, bot FROM `".$table_301."`");
    $test=false;
    foreach ($sql as $valeur) {
      if ($valeur->bot != 0) { 
        if ($test==false) $output .= 'User-agent: *'."\n";
        $output .= "Disallow: $path$valeur->tag".'$'."\n";
        $test=true;
      }
		}
	}
  return $output;
}
add_filter('robots_txt','my_bots',0);
/* Fin de gestion du robots */
?>