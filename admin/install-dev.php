<?php
if (file_exists('../inc/config.yml'))
{
	header('Location: index.php');
	exit;
}
session_name('adminsession');
session_start();
require_once '../inc/libs/functions.php';
require_once '../inc/libs/lang.class.php';
require_once '../inc/libs/db-mysql.php';
require_once '../inc/apps.php';
require_once '../inc/libs/YAML/autoload.php';
use Symfony\Component\Yaml\Yaml;

$lang = new lang();
$lang->setLangFolder('lang/');
?>
<html>
<head>
	<title>Installation</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="assets/admin.css" type="text/css">
	<script src="assets/js/jquery.min.js"></script>
	<script>
        function fadeInput(input) {
            $('#' + input).fadeToggle(200);
        }
	</script>
</head>
<body style="background: url('assets/bglogin.jpg') no-repeat center fixed;">
<img src="assets/Logo.svg" alt="Mowie" class="install-logo"/>
<h1 style="text-align: center; color: #E8E8E8;">Installation</h1>
<?php
if (isset($_POST['submit']))
{
	if (
		$_POST['lang'] !== '' &&
		$_POST['db_host'] !== '' &&
		$_POST['db_name'] !== '' &&
		$_POST['db_user'] !== '' &&
		$_POST['db_pw'] !== '' &&
		$_POST['general_webUrl'] !== '' &&
		$_POST['general_home_url'] !== '' &&
		$_POST['general_page_title'] !== '' &&
		$_POST['general_template'] !== '' &&
		$_POST['admin_name'] !== '' &&
		$_POST['admin_mail'] !== '' &&
		$_POST['admin_pw1'] !== '' &&
		$_POST['admin_pw2'] !== ''
	)
	{
		$CONFIG = [];
		$CONFIG['General']['web_uri'] = $_POST['general_webUrl'];
		$CONFIG['General']['home_uri'] = $_POST['general_home_url'];
		$CONFIG['General']['phpmyadmin'] = $_POST['general_pma'];
		$CONFIG['General']['title'] = 'inc/System/page_title.txt';
		$CONFIG['General']['tinymce_css'] = $_POST['general_editor_css'];
		$CONFIG['Database']['db_type'] = 'mysql';
		$CONFIG['Database']['db_host'] = $_POST['db_host'];
		$CONFIG['Database']['db_name'] = $_POST['db_name'];
		$CONFIG['Database']['db_usr'] = $_POST['db_user'];
		$CONFIG['Database']['db_pw'] = $_POST['db_pw'];
		$CONFIG['Database']['db_prefix'] = $_POST['db_prefix'];
		$CONFIG['Templating']['template'] = $_POST['general_template'];
		$CONFIG['Templating']['tpl_title'] = 'title';
		$CONFIG['Templating']['tpl_content'] = 'content';
		$CONFIG['Templating']['tpl_webUri'] = 'website_uri';
		$CONFIG['Versioning']['version'] = '0.96';
		$CONFIG['Versioning']['version_num'] = 9;
		$CONFIG['Versioning']['update_uri'][] = 'https://cdn.kola-entertainments.de/cms/';

		$CONFIG['Mail']['smtp'] = false;
		//Mail Settings
		if (isset($_POST['mail_smtp']))
		{
			if ($_POST['mail_host'] != '' && $_POST['mail_user'] != '' && $_POST['mail_pass'] != '' && $_POST['mail_secure'] != '' && $_POST['mail_port'] != '')
			{
				$CONFIG['Mail']['smtp'] = true;
				$CONFIG['Mail']['host'] = $_POST['mail_host'];
				$CONFIG['Mail']['username'] = $_POST['mail_user'];
				$CONFIG['Mail']['password'] = $_POST['mail_pass'];
				$CONFIG['Mail']['secure'] = $_POST['mail_secure'];
				$CONFIG['Mail']['port'] = $_POST['mail_port'];
			} else
			{
				echo msg('fail', 'Please provide all SMTP-Informations.');
				exit;
			}
		}

		//Test Passwords
		if ($_POST['admin_pw1'] !== $_POST['admin_pw2'])
		{
			echo msg('fail', 'Adminpasswords don\'t match');
			exit;
		}

		//Database
		$db = new db($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pw'], $_POST['db_prefix']);

		//Create Tables
		if ($db->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `' . $_POST['db_prefix'] . 'system_admins` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `pass` text NOT NULL,
  `lvl` text NOT NULL,
  `mail` text NOT NULL,
  `secret` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `' . $_POST['db_prefix'] . 'system_loggedin` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `user_agent` longtext NOT NULL,
  `ip` text NOT NULL,
  `time` int(11) NOT NULL,
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `' . $_POST['db_prefix'] . 'system_roles` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `permissions` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `' . $_POST['db_prefix'] . 'system_show_stream` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `level` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `' . $_POST['db_prefix'] . 'system_stream` (
  `id` int(11) NOT NULL,
  `time` text CHARACTER SET latin1 NOT NULL,
  `user` text CHARACTER SET latin1 NOT NULL,
  `lvl` text CHARACTER SET latin1 NOT NULL,
  `message` longtext CHARACTER SET latin1 NOT NULL,
  `extra` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `' . $_POST['db_prefix'] . 'system_show_stream`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `' . $_POST['db_prefix'] . 'system_stream`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `' . $_POST['db_prefix'] . 'system_show_stream`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `' . $_POST['db_prefix'] . 'system_stream`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `' . $_POST['db_prefix'] . 'system_admins`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `' . $_POST['db_prefix'] . 'system_loggedin`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `' . $_POST['db_prefix'] . 'system_roles`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `' . $_POST['db_prefix'] . 'system_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `' . $_POST['db_prefix'] . 'system_loggedin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `' . $_POST['db_prefix'] . 'system_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
')
		)
		{
			echo msg('success', 'Created Tables successfully.');
		} else
		{
			echo msg('fail', 'Failed Creating Tables');
			exit;
		}
		//Admin group
		$db->setCol('system_roles');
		$db->data['name'] = 'Admins';
		if ($db->insert())
		{
			echo msg('success', 'Successfully created admin group.');
		} else
		{
			echo msg('fail', 'Error creating admin group.');
			exit;
		}
		//Admin User
		$db->setCol('system_admins');
		$db->data['username'] = $_POST['admin_name'];
		$db->data['pass'] = password_hash($_POST['admin_pw1'], PASSWORD_DEFAULT);
		$db->data['lvl'] = 1;
		$db->data['mail'] = $_POST['admin_mail'];
		if ($db->insert())
		{
			echo msg('success', 'Successfully created admin user.');
		} else
		{
			echo msg('fail', 'Error creating admin user.');
			exit;
		}

		//Page title
		if (file_put_contents('../inc/System/page_title.txt', $_POST['general_page_title']))
		{
			echo msg('success', 'Page Title was successfully set.<br/>');
		} else
		{
			echo msg('fail', 'Error setting page title.');
			exit;
		}

		//htacces
		if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false)
		{
			$htacces = 'RewriteEngine On

RewriteRule ^(admin|inc|apps|content)($|/) - [L]
RewriteRule !favicon\.ico - [C]
RewriteRule !index\.php - [C]
RewriteRule ^(.*)$ /index.php?$1 [QSA,L]

# Disables download of configuration
<Files ~ "\.(tpl|yml|ini)$">
    <IfModule mod_authz_core.c>
          Require all denied
    </IfModule>
</Files>';
			if (file_put_contents('../.htaccess', $htacces))
			{
				echo msg('success', '.htaccess was successfully set.<br/>');
			} else
			{
				echo msg('fail', 'Error setting up .htaccess.<br/>');
				exit;
			}
		} else
		{
			echo msg('info', 'We detected you are not using Apache. Please make sure to redirect all requests to index.php (Like Apache\'s mod_rewrite).');
		}

		//Apps
		$apps = new apps(2);
		$appUri = '../apps/';
		foreach ($apps->getApps() as $app => $appconf)
		{
			require $appUri . $appconf['app_path'] . '/config.php';
			if (isset($_CONF['install']) && $_CONF['install'] != '' && file_exists($appUri . $appconf['app_path'] . '/' . $_CONF['install']))
			{
				require $appUri . $appconf['app_path'] . '/' . $_CONF['install'];
			}
		}
		//Write Config
		$configfile = Yaml::dump($CONFIG);
		if (file_put_contents('../inc/config.yml', $configfile))
		{
			echo msg('success', 'Configfile was successfully created.');
		} else
		{
			echo msg('fail', 'Error creating configfile.');
			exit;
		}
		echo msg('info', 'Installation successfully completed. <a href="' . $_POST['general_webUrl'] . 'admin">Login</a>');
	} else
	{
		echo msg('info', 'Please fill in all fields!');
	}
} else
{
	?>

	<div class="install-container">
		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form">
			<h2>Language</h2>
			<span>Select your language:</span>
			<select name="lang">
				<?php
				$langs = $lang->getAll();
				foreach ($langs as $lang_code => $lang_detail)
				{
					echo '<option value="' . $lang_code . '">' . $lang_detail['Lang'] . '</option>';
				}
				?>
			</select><br/><br/>

			<h2>Mysql</h2>
			<span>Host</span><input type="text" placeholder="Host" name="db_host" value="localhost"/><br/>
			<span>Database</span><input type="text" placeholder="Database" name="db_name"/><br/>
			<span>Username</span><input type="text" placeholder="Username" name="db_user" value="root"/><br/>
			<span>Password</span><input type="password" placeholder="Password" name="db_pw"/><br/>
			<span>Table prefix (optional)</span><input type="text" placeholder="Table prefix" name="db_prefix"/><br/><br/>

			<h2>Website</h2>
			<span>Page Title</span><input type="text" placeholder="Page Title" name="general_page_title"/><br/>
			<span>Website Url</span><input type="text" placeholder="Website Url" name="general_webUrl"
										   value="http://<?php echo $_SERVER['SERVER_NAME'] . str_replace('admin/install.php', '', $_SERVER['REQUEST_URI']); ?>"/><br/>
			<span>&nbsp;</span><a onclick="fadeInput('more');" style="display: block;">More Options</a><br/>
			<div id="more" style="display: none;">
				<span>Home Url</span><input type="text" placeholder="Home Url" name="general_home_url"
											value="<?php echo str_replace('admin/install.php', '', $_SERVER['REQUEST_URI']); ?>"/><br/>
				<span>Phpmyadmin Url (optional)</span><input type="text" placeholder="Phpmyadmin Url" name="general_pma"/><br/>
				<span>Editor CSS (optional)</span><input type="text" placeholder="Editor CSS" name="general_editor_css"/><br/>
				<span>Template</span><input type="text" placeholder="Template" name="general_template"
											value="content/template.tpl"/><br/>
			</div>
			<h2>Mail</h2>
			<span>&nbsp;</span><input type="checkbox" name="mail_smtp" id="mail_smtp"
									  onchange="fadeInput('mailInput');"/><label for="mail_smtp"><i></i>
				Use SMTP</label>

			<br/>
			<div id="mailInput" style="display: none">
				<span>SMTP-Host</span><input type="text" placeholder="SMTP-Host" name="mail_host"/><br/>
				<span>SMTP-Username</span><input type="text" placeholder="SMTP-Username" name="mail_user"/><br/>
				<span>SMTP-Password</span><input type="text" placeholder="SMTP-Password" name="mail_pass"/><br/>
				<span>Security</span>
				<input type="radio" name="mail_secure" id="mail_ssl"/><label for="mail_ssl"><i></i> Use SSL</label>
				<input type="radio" name="mail_secure" id="mail_tls"/><label for="mail_tls"><i></i> Use TLS</label>
				<br/>
				<span>Port</span><input type="number" placeholder="Port" name="mail_port"/>

				<br/>
			</div>

			<h2>First Adminuser</h2>
			<span>Name</span><input type="text" placeholder="Name" name="admin_name"/><br/>
			<span>Email-Adress</span><input type="email" placeholder="Email-Adress" name="admin_mail"/><br/>
			<span>Password</span><input type="password" placeholder="Password" name="admin_pw1"/><br/>
			<span>Confirm Password</span><input type="password" placeholder="Confirm Password" name="admin_pw2"/><br/>
			<?php
			//Apps
			$apps = new apps(2);
			$appUri = '../apps/';
			foreach ($apps->getApps() as $app => $appconf)
			{
				require $appUri . $appconf['app_path'] . '/config.php';
				if (isset($_CONF['install']) && $_CONF['install'] != '' && file_exists($appUri . $appconf['app_path'] . '/' . $_CONF['install']))
				{
					require $appUri . $appconf['app_path'] . '/' . $_CONF['install'];
				}
			}
			?>
			<p style="text-align: center"><input type="submit" value="Install" name="submit"/></p>
		</form>

	</div>
	<?php
}
?>
</body>
</html>