<?php
/* Chargement de la liste déroulante redirection 404 */
function listing($cherche,$url) {
  $debase = get_option('tag_base') ."/";
  $cherche = substr($cherche, strrpos($cherche,"/")+1);
  if (strrpos($cherche, ".") !== false ) $cherche = substr($cherche, 0,strrpos($cherche,"."));
  $cherche =  explode("-",$cherche);
  global $wpdb;
  $message .= '<option> </option>';
  foreach ($cherche as $valeur) {
    if (strlen($valeur) >= 4) {
      $sql_search = "SELECT `slug` FROM `".$wpdb->prefix."terms` WHERE `slug` LIKE '%".$valeur."%'";
      $result = $wpdb->get_results($sql_search);
      if ( !empty($result ) ) {
        foreach ($result as $liste) {
          $message .= '<option>'.$url.$debase.$liste->slug.'</option>';
        }
      }
    }
  }
  foreach ($cherche as $valeur) {
    $sql_search = 'SELECT T4.ID, T4.guid FROM'
    . ' `'.$wpdb->prefix.'terms` as T1,'
    . ' `'.$wpdb->prefix.'term_taxonomy` as T2,'
    . ' `'.$wpdb->prefix.'term_relationships` as T3,'
    . ' `'.$wpdb->prefix.'posts` as T4'
    . ' WHERE'
    . ' T1.slug = \''.$valeur.'\''
    . ' AND T2.term_id = T1.term_id'
    . ' AND T2.taxonomy = \'post_tag\''
    . ' AND T3.term_taxonomy_id = T2.term_taxonomy_id'
    . ' AND T4.id = T3.object_id'
    . ' AND T4.post_type = \'post\''
    . ' AND T4.post_status = \'publish\''
    . ' ORDER BY T4.post_date DESC ';
    $result = $wpdb->get_results($sql_search);
    if ( !empty($result) ) {
      foreach ($result as $liste) {
        $message .= '<option>'.get_permalink($liste->ID).'</option>';
      }
    }
  }
  return $message;
}

/* Test des permaliens par défaut */
function PMB_not_found() {
  if ( get_option('permalink_structure', false) == false )  {
    echo '<h2 style="color:red; font-height:bold; text-align:center; line-height:150%">';
    echo __("We recommend changing the default format of your permalinks","panel-my-blog") . "<br />";
    echo __("Referencing your Wordpress blog will be even better !","panel-my-blog") . "<br />";
    echo __("Otherwise plugin PANEL-MY-BLOG you do not need.","panel-my-blog").'</h2>';
    exit;
  }
}
?>
