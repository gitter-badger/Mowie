<?php
$_CONF['app_name'] = 'Logfiles';
$_CONF['app_desc'] = 'Logfiles';
$_CONF['app_build'] = 1;
$_CONF['app_version'] = 'v0.9 Beta';
//$_CONF['base_url'] = 'demomodul/'; //Basisurl des moduls, wenn es über das Frontend aufgerufen wird
$_CONF['base_file'] = 'log.php'; //Datei, die angezeigt wird, wenn die basisurl aufgerufen wird
$_CONF['menu_top'] = '<i class="fa fa-server"></i>  '.$lang->get('log_title'); //Name des Moduls, wie es im Adminbereich im Hauptmenü auftaucht
$_CONF['menu'] = ['menu_top' => 'index.php']; //Ein Array mit menüpunkten im Backoffice
//$_CONF['dashboard'] = 'dashboard.php'; //Optional, eine Datei, welche im admindashboard angezeigt wird
$_CONF['type'] = 'static';
$_CONF['install'] = 'install.php';
?>