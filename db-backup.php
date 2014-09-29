<?php

/***********************
 * Backup Script to be run by cron or from command line to run Magento Backup 
 ***********************/

$mageapp  = '/opt/bitnami/apps/magento/htdocs/app/Mage.php';       // change to your Mage app location
$logfile  = 'backup.log';       // Mage Backup log file

require_once $mageapp;
umask(0);
Mage::app('admin');
 
$session = Mage::getSingleton('adminhtml/session');
 
$backupDb = Mage::getModel('backup/db');
$backup = Mage::getModel('backup/backup')
->setTime(time())
->setType('db')
->setPath(Mage::getBaseDir("var") . DS . "backups");
 
Mage::register('backup_model', $backup);

Mage::log('Backup started.', null, $logfile);
 
$backupDb->createBackup($backup);

Mage::log('Backup complete.', null, $logfile);
echo "Backup Complete.\n";
 
?>