<?php
$GLOBALS['lang']->set('Dateiverwaltung', 'files_title', 'de');
$GLOBALS['lang']->set('Manage Files', 'files_title', 'en');

$_CONF['mod_name'] = 'Files';
$_CONF['mod_desc'] = 'Ein Modul zum Anzeigen & Uploaden von Dateien';
$_CONF['menu_top'] = '<i class="icon-folder2"></i> '.$GLOBALS['lang']->get('files_title');
$_CONF['menu'] = ['menu_top' => 'index.php'];
$_CONF['type'] = 'none';
