<?php
/**
 * Magento Maintenance Script - Laurence Tunnicliffe / Crucial web
 *
 */

 if (isset($argv)) {
    $domain = $argv[1];
}
else {
    $domain = $_GET['domain'];
}
 
dev($domain);

function dev($domain) {
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);
    
    if(is_object($xml)) {
        $db['host'] = $xml->global->resources->default_setup->connection->host;
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;
        $db['user'] = $xml->global->resources->default_setup->connection->username;
        $db['pass'] = $xml->global->resources->default_setup->connection->password;
        $db['pref'] = $xml->global->resources->db->table_prefix;
        
		$cleanTables = array(
			'sales_flat_creditmemo',
			'sales_flat_creditmemo_comment',
			'sales_flat_creditmemo_grid',
			'sales_flat_creditmemo_item',
			'sales_flat_invoice',
			'sales_flat_invoice_comment',
			'sales_flat_invoice_grid',
			'sales_flat_invoice_item',
			'sales_flat_order',
			'sales_flat_order_address',
			'sales_flat_order_grid',
			'sales_flat_order_item',
			'sales_flat_order_payment',
			'sales_flat_order_status_history',
			'sales_flat_quote',
			'sales_flat_quote_address',
			'sales_flat_quote_address_item',
			'sales_flat_quote_item',
			'sales_flat_quote_item_option',
			'sales_flat_quote_payment',
			'sales_flat_quote_shipping_rate',
			'sales_flat_shipment',
			'sales_flat_shipment_comment',
			'sales_flat_shipment_grid',
			'sales_flat_shipment_item',
			'sales_flat_shipment_track',
			'sales_invoiced_aggregated',
			'sales_invoiced_aggregated_order', 
			'log_quote',
			'eav_entity_store',
			'customer_address_entity',
			'customer_address_entity_datetime',
			'customer_address_entity_decimal',
			'customer_address_entity_int',
			'customer_address_entity_text',
			'customer_address_entity_varchar',
			'customer_entity',
			'customer_entity_datetime',
			'customer_entity_decimal',
			'customer_entity_int',
			'customer_entity_text',
			'customer_entity_varchar',
			'tag',
			'tag_relation',
			'tag_summary',
			'tag_properties',
			'wishlist',
			'log_customer',
			'log_url',
			'log_url_info',
			'log_visitor',
			'log_visitor_info',
			'report_event',
			'report_viewed_product_index',
			'sendfriend_log'
        );
        
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());
        
		@mysql_query('SET FOREIGN_KEY_CHECKS=0');
		
        foreach($cleanTables as $table) {
            @mysql_query('TRUNCATE `'.$db['pref'].$table.'`');
			@mysql_query('ALTER TABLE `'.$db['pref'].$table.'` AUTO_INCREMENT=1');
        }
		
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = "lt@sixbondstreet.com" WHERE value LIKE "%@sixbondstreet.com" AND (path LIKE "contacts/%" OR path LIKE "sales_email/%")'); //Send emails to me
		
		//payment test mode here @mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = "lt@sixbondstreet.com" WHERE value LIKE "%@sixbondstreet.com" AND (path LIKE "contacts/%" OR path LIKE "sales_email/%");'); //Send emails to me
		
		//@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = REPLACE(value, "sixbondstreet.com", "sixbond.info") value = REPLACE(value, "assets.example.com", "assets.example.local") WHERE value LIKE "%sixbondstreet.com%"'); //Core shit

		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="http://'.$domain.'/" WHERE  `config_id`=2'); //Unsecure web root
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="https://'.$domain.'/" WHERE  `config_id`=3'); //Secure web root
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="'.$domain.'" WHERE  `config_id`=238'); //Unsecure web root
		
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`= NULL WHERE  `config_id`=2421'); //Turn off Image CDN
		
		@mysql_query('UPDATE `'.$db['pref'].'core_cache_option` SET value = 0 WHERE value = 1'); //Cache off

		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = 0 WHERE path = "google/analytics/active"'); //GA off

		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = 1 WHERE path = "design/head/demonotice"'); //GA off

		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET value = REPLACE(value, "logo.png", "dev-logo.png") WHERE value LIKE "%logo.png%"'); //Dev logo

		@mysql_query('SET FOREIGN_KEY_CHECKS=1');
		
		 $dirs = array(
			'downloader/.cache/',
			'downloader/pearlib/cache/*',
			'downloader/pearlib/download/*',
			'media/css/',
			'media/css_secure/',
			'media/import/',
			'media/js/',
			'var/cache/',
			'var/locks/',
			'var/log/',
			'var/report/',
			'var/session/',
			'var/tmp/'
		);
		
		foreach($dirs as $dir) {
			exec('rm -rf '.$dir);
		}
		
		echo 'Complete';
		
    } else {
        exit('Unable to load local.xml file');
    }
}



