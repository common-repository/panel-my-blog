<?php
if ( !defined( 'ABSPATH') &&  !defined('WP_UNINSTALL_PLUGIN') )  exit();

delete_option('_GestBlog_settings');
global $wpdb;
$tb_301 = $wpdb->prefix . 'gestblog_301';
$tb_iplist = $wpdb->prefix . 'gestblog_iplist';
$tb_robot = $wpdb->prefix . 'gestblog_robot';
$wpdb->query("DROP TABLE `".$tb_301."`"); 
$wpdb->query("DROP TABLE `".$tb_iplist."`"); 
$wpdb->query("DROP TABLE `".$tb_robot."`"); 
?>
