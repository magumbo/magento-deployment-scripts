<?php
/**
 * Magento Maintenance Script - Laurence Tunnicliffe
 *
 */

$cloudFrontDist = 'dth6xvz55i4as.cloudfront.net';
 
$build = ''; //Blank if none set -will revert to old skin / backup
 
if (isset($argv)) {
    $build = $argv[1];
}
else {
    $build = $_GET['build'];
}
 
dev($build, $cloudFrontDist);

function dev($build, $cloudFrontDist) {
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);
    
    if(is_object($xml)) {
        $db['host'] = $xml->global->resources->default_setup->connection->host;
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;
        $db['user'] = $xml->global->resources->default_setup->connection->username;
        $db['pass'] = $xml->global->resources->default_setup->connection->password;
        $db['pref'] = $xml->global->resources->db->table_prefix;
        
        
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());
        
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="http://'.$cloudFrontDist.'/skin-'.$build.'/" WHERE  `config_id`=2587'); //Unsecure skin
		
		@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="https://'.$cloudFrontDist.'/skin-'.$build.'/" WHERE  `config_id`=2586'); //Secure skin

		//@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="http://'.$cloudFrontDist.'/js-'.$build.'/" WHERE  `config_id`=218'); //Unsecure js
		
		//@mysql_query('UPDATE `'.$db['pref'].'core_config_data` SET `value`="https://'.$cloudFrontDist.'/js-'.$build.'/" WHERE  `config_id`=225'); //Secure js
		
		
		$dirs = array(
			'var/cache/',
			'var/tmp/'
		);
		
		foreach($dirs as $dir) {
			exec('rm -rf '.$dir);
		}
		
		apc_clear_cache();
		apc_clear_cache('user');
		apc_clear_cache('opcode');
		
		echo 'Complete';
		
    } else {
        exit('Unable to load local.xml file');
    }
}



