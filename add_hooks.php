<?php
/**********************************************************************************
* add_remove_hooks.php                                                            *
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
***********************************************************************************
* This file is a simplified database installer. It does what it is suppoed to.    *
**********************************************************************************/

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
if (SMF == 'SSI')
	db_extend('packages');
	
// Define the hooks
$hook_functions = array(
  'integrate_pre_include' . '@' . '$sourcedir/Class-Spoiler.php',
  'integrate_pre_include' . '@' . '$sourcedir/Subs-PMAttachmentsHooks.php',
  'integrate_pre_include' . '@' . '$sourcedir/Class-InfoCenterMgr.php',
  'integrate_pre_include' . '@' . '$sourcedir/TeleTypeBBC.php',
	'integrate_actions' . '@' . 'PMA_Actions',
	'integrate_admin_areas' . '@' . 'PMA_Admin',
  'integrate_admin_areas' . '@' . 'PeakFox\InfoCenterMgr::adminAreas#',
	'integrate_admin_search' . '@' . 'PMA_Admin_Search',
  'integrate_bbc_buttons' . '@' . 'tt_bbc_buttons',
  'integrate_bbc_codes' . '@' . 'tt_bbc_codes',
  'integrate_credits' . '@' . 'tt_credits',
	'integrate_load_illegal_guest_permissions' . '@' . 'PMA_Non_Guest',
	'integrate_load_permissions' . '@' . 'PMA_Permissions',
	'integrate_load_permission_levels' . '@' . 'PMA_Load_Levels',
	'integrate_manage_attachments' . '@' . 'PMA_Manage',
  'integrate_modify_modifications' . '@' . 'PeakFox\InfoCenterMgr::prepSettings#',
  'integrate_mark_read_button' . '@' . 'PeakFox\InfoCenterMgr::markReadHook#',
  'integrate_pre_css_output' . '@' . 'tt_pre_css_output',
  'integrate_pre_load' . '@' . 'Spoiler::hooks#',
  'integrate_pre_load' . '@' . 'tt_pre_load'
);

	$call = 'add_integration_function';

// Do the deed
foreach ($hook_functions as $hookRaw)
{
  $exploded = explode('@', $hookRaw, 2);
	$call($exploded[0], $exploded[1]);
}

if (SMF == 'SSI')
   echo 'Hooks installed.';

?>