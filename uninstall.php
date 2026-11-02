<?php
/**
 * Очистка базы данных при удалении плагина через админку WordPress.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'meta_tags';

$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
