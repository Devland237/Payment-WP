<?php

class Woom_Table{
	public static function woom_install()
	{
		require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$table_name = $wpdb->prefix.'viazipay';
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name. '(
			id int(11) NOT NULL AUTO_INCREMENT,
			id_order varchar(240) NOT NULL,
			payment_url text NOT NULL,
			token text NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB '. $charset_collate;

		dbDelta($sql);
	}
	
	public static function woom_insert_data( $data = [] )
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'viazipay';
		if(!empty($data)){
			$wpdb->INSERT( $table_name, $data );
		}else{
			exit;
		}
	}
	
	public static function woom_retrieve_data($id_order)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'viazipay';
		$result = $wpdb->get_results("SELECT payment_url, token FROM $table_name WHERE id_order = $id_order");
		
		return $result[0];
	}

	public static function woom_update_data($data = [], $order_id)
    {

        global $wpdb;
		$table_name = $wpdb->prefix . 'viazipay';

        $wpdb->UPDATE($table_name, $data, array('id_order'=> $order_id));
    }

	public static function woom_delete_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix.'viazipay';
		$wpdb->query( 'DROP TABLE IF EXISTS'. $table_name );
	}
}