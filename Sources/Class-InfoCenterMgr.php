<?php

/*
 * Class-InfoCenterMgr.php
 *
 * @package InfoCenterMgr
 * @author PeakFox
 * @copyright 2022 PeakFox
 * @version 1.0b2 (2022-10-24)
 * @license https://spdx.org/licenses/GPL-3.0-or-later.html GPL-3.0-or-later
 */

namespace PeakFox;

final class InfoCenterMgr
{
	// @hook integrate_admin_areas
	// Add this mod's configuration panel to the admin menu
	//
	public static function adminAreas(array &$admin_areas) : void
	{
		global $txt;

		loadLanguage('InfoCenterMgr/lang');
		
		$admin_areas['config']['areas']['modsettings']['subsections']['InfoCenterMgr'] =
			[$txt['icm_title']];
	}
	
	
	// @hook integrate_modify_modifications
	// Add settings to the configuration panel
	//	
	public static function prepSettings(array &$subActions) : void
	{
		$subActions['InfoCenterMgr'] = __CLASS__ . '::settings';
	}
	
	
	// Generate configuration settings for admin panel
	public static function settings($return_config = false)
	{
		global $context, $txt, $scripturl, $smcFunc;

		$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=InfoCenterMgr';

		// Compose the configuration panel
		$context['settings_title'] = $txt['icm_settings_title'];
		$config_vars = [
			['check', 'icm_hide_online'],
			['check', 'icm_limit_recent_posts']
		];
		
		// Return config vars
		if ($return_config)
			return $config_vars;
		
		// Save settings			
		if (isset($_REQUEST['save']))
		{
			checkSession();
			saveDBSettings($config_vars);
				
			// Redirect to config panel
			redirectexit('action=admin;area=modsettings;sa=InfoCenterMgr');
		}
		
		prepareDBSettingContext($config_vars);
	}
	
	
	// @hook integrate_mark_read_button
	// Called after "Info Center" sections have been compiled
	//
	public static function markReadHook() : void
	{
		global $modSettings, $context, $smcFunc, $scripturl;
		
		// "Hide users online" feature
		if (! empty($modSettings['icm_hide_online']))
		{
			// Search for "Users Online" section in Info Center content
			if (isset($context['info_center']))
			{
				$res = array_keys(
					array_filter(
						$context['info_center'], 
						function($item) { return $item['tpl'] === 'online'; }
					)
				);
				
				// If the "Users Online" section exists, then remove it
				if (isset($res[0]))
					unset($context['info_center'][$res[0]]);
			}
		}

		// As of now, we need to retrieve the recent posts again to implement our
		// "limit to X latest replies" feature. Only do this if the feature is enabled
		// to begin with.	
		if (empty($modSettings['icm_limit_recent_posts']))
			return;	
		
		// Since $settings['number_recent_posts'] is not available to us, 
		// we infer the number of latest posts from the number of previously 
		// returned latest posts in $context
		$number_recent_posts = count($context['latest_posts']);
		
		// To avoid fetching all posts in the board, we estimate a conservative upper limit
		// on the number of posts to retrieve from the DB
		$query_limit = $number_recent_posts * 100;
		
		// The following query and processing code basically replicates what is in Subs-Recents.php, but
		// with modifications for our purposes
		
		// Fetch those latest posts from DB
		$req = $smcFunc['db_query']('', '
			SELECT
				m.poster_time, mfirst.subject, m.id_topic, m.id_member, m.id_msg,
				COALESCE(mem.real_name, m.poster_name) AS poster_name, t.id_board, b.name AS board_name
			FROM {db_prefix}messages AS m
				INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
        INNER JOIN {db_prefix}messages AS mfirst ON (mfirst.id_msg = t.id_first_msg)
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
			WHERE m.id_msg >= 0' .
				(!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? '
				AND b.id_board != {int:recycle_board}' : '') . '
				AND {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
				AND t.approved = 1
				AND m.approved = 1' : '') . '
			ORDER BY
				m.id_msg DESC
			LIMIT
				{int:query_limit}',
			array(
				'recycle_board' => $modSettings['recycle_board'],
				'query_limit' => $query_limit
			)
		);

		// Create the "latest posts" array, but in this code we limit topic entries to the amount of
		// 'icm_limit_recent_posts' latest replies (set in mod's config panel)
		$posts = array();
		$topic_seen = array();
		$total_posts = 0;
		while (($row = $smcFunc['db_fetch_assoc']($req)) && ($total_posts < $number_recent_posts))
		{		
			// Handle and check reply counts for individual topics
			$id_topic = $row['id_topic'];
			if (empty($topic_seen[$id_topic]))
			{
				// First post for this topic
				$topic_seen[$id_topic] = 1;
			}
			else
			{
				// Topic already seen; ignore this row
				continue;
			}
			
			// Censor the subject and post for the preview
			censorText($row['subject']);

			// Build the array.
			$id_board = $row['id_board'];
			$id_msg = $row['id_msg'];
			$poster_time = $row['poster_time'];
			$posts[] = array(
				'board' => array(
					'id' => $id_board,
					'name' => $row['board_name'],
					'href' => $scripturl . '?board=' . $id_board . '.0',
					'link' => '<a href="' . $scripturl . '?board=' . $id_board . '.0">' . $row['board_name'] . '</a>'
				),
				'topic' => $id_topic,
				'poster' => array(
					'id' => $row['id_member'],
					'name' => $row['poster_name'],
					'href' => empty($row['id_member']) ? '' : $scripturl . '?action=profile;u=' . $row['id_member'],
					'link' => empty($row['id_member']) ? $row['poster_name'] : '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>'
				),
				'subject' => $row['subject'],
				'preview' => '',
				'short_subject' => shorten_subject($row['subject'], 24),
				'time' => timeformat($poster_time),
				'timestamp' => $poster_time,
				'raw_timestamp' => $poster_time,
				'href' => $scripturl . '?topic=' . $id_topic . '.msg' . $id_msg . ';topicseen#msg' . $id_msg,
				'link' => '<a href="' . $scripturl . '?topic=' . $id_topic . '.msg' . $id_msg . ';topicseen#msg' . $id_msg . '" rel="nofollow">' . $row['subject'] . '</a>'
			);
			
			// Increment the total row count
			$total_posts ++;
		}		

		// Finally, overwrite the previous version of 'latest_posts' with our filtered version
		$context['latest_posts'] = $posts;
		$smcFunc['db_free_result']($req);	
	}
}
