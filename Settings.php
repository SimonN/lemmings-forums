<?php

/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

########## Maintenance ##########
# Note: If $maintenance is set to 2, the forum will be unusable!  Change it to 0 to fix it.
$maintenance = 0;		# Set to 1 to enable Maintenance Mode, 2 to make the forum untouchable. (you'll have to make it 0 again manually!)
$mtitle = 'Maintenance Mode';		# Title for the Maintenance Mode message.
$mmessage = 'The forum software is currently being upgraded. Check back soon.';		# Description of why the forum is in maintenance mode.

########## Forum Info ##########
$mbname = 'Lemmings Forums';		# The name of your forum.
$language = 'english';		# The default language file set for the forum.
$boardurl = 'https://www.lemmingsforums.net';		# URL to your forum's folder. (without the trailing /!)
$webmaster_email = 'noreply@lemmingsforums.net';		# Email address to send emails from. (like noreply@yourdomain.com.)
$cookiename = 'LFCookie42';		# Name of the cookie to set for authentication.

########## Database Info ##########
$db_type = 'mysql';
$db_server = 'localhost';
$db_name = 'LemForums';
$db_user = 'LemForums';
$db_passwd = 'password';
$ssi_db_user = '';
$ssi_db_passwd = '';
$db_prefix = 'lf_';
$db_persist = 0;
$db_error_send = 0;

########## Directories/Files ##########
# Note: These directories do not have to be changed unless you move things.
$boarddir = '';		# The absolute path to the forum's folder. (not just '.'!)
$sourcedir = 'Sources';		# Path to the Sources directory.
$cachedir = 'cache';		# Path to the cache directory.

########## Error-Catching ##########
# Note: You shouldn't touch these settings.
$db_last_error = 0;


# Make sure the paths are correct... at least try to fix them.
if (!file_exists($boarddir) && file_exists(dirname(__FILE__) . '/agreement.txt'))
	$boarddir = dirname(__FILE__);
if (!file_exists($sourcedir) && file_exists($boarddir . '/Sources'))
	$sourcedir = $boarddir . '/Sources';
if (!file_exists($cachedir) && file_exists($boarddir . '/cache'))
	$cachedir = $boarddir . '/cache';

$db_character_set = 'utf8';
$upgradeData = "YTo4OntzOjI6ImlkIjtzOjE6IjEiO3M6NDoibmFtZSI7czo2OiJuYW1pZGEiO3M6NDoicGFzcyI7aTo3ODk4O3M6Nzoic3RhcnRlZCI7aToxNTU4MDU5NDQzO3M6NzoidXBkYXRlZCI7aToxNTU4MDYwNzQ3O3M6NzoidmVyc2lvbiI7czo1OiIyLjAuOSI7czo0OiJzdGVwIjtpOjM7czo3OiJzdWJzdGVwIjtpOjA7fQ==";
$image_proxy_secret = '9b9c2b37f942bb4be9d3';
$image_proxy_maxsize = 5190;
$auth_secret = '5e8a43052fa1c935d3b6f9c131008958b72b454c9834bb5ca994e71a1310165c';
?>
