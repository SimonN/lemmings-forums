<?php

/*******************************************************************************
 * TeleTypeBBC
 *
 * A modification for Simple Machines Forum 2.1 that restores the [tt] BBCode.
 *
 * Copyright (c) 2023 Jon Stovell
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 ******************************************************************************/


if (!defined('SMF'))
	die('No direct access...');

/**
 * Removes 'tt' from $context['legacy_bbc']
 *
 * Called by:
 *      integrate_pre_load
 */
function tt_pre_load()
{
	global $context;

	$context['legacy_bbc'] = array_diff($context['legacy_bbc'], array('tt'));
}

/**
 * Tells SMF how to style the output.
 */
function tt_pre_css_output()
{
	loadCSSFile('ttbbc.css', array('minimize' => true), 'smf_ttbbc');
}

/**
 * Tweaks parse_bbc() settings for [tt]
 *
 * Called by:
 *      integrate_bbc_codes
 */
function tt_bbc_codes(&$codes, &$no_autolink_tags)
{
	// Prevent autolinking of URLs inside [tt] BBC.
	$no_autolink_tags[] = 'tt';

	// Add some nice extra styling.
	foreach ($codes as &$code)
	{
		if ($code['tag'] === 'tt')
		{
			$code['before'] = '<span class="monospace bbc_tt">';
		}
	}
}

/**
 * Adds a [tt] button to the editor toolbar
 *
 * Called by:
 *      integrate_bbc_buttons
 */
function tt_bbc_buttons(&$bbc_tags, &$editor_tag_map)
{
	global $context, $editortxt;

	tt_editortxt();

	foreach ($context['bbc_tags'] as $row_num => $row)
	{
		$temp = array();

		foreach ($row as $tag)
		{
			$temp[] = $tag;

			if (isset($tag['code']) && $tag['code'] === 'code')
			{
				$temp[] = array(
					'image' => 'tt',
					'code' => 'tt',
					'description' => $editortxt['tt'],
				);
			}
		}

		$context['bbc_tags'][$row_num] = $temp;
	}

	loadJavaScriptFile('ttbbc.js', array('minimize' => true), 'smf_ttbbc');
}

/**
 * Although it does not follow the usual approach, this function provides broad
 * language support without littering the language directory with a bunch of
 * unnecessary files for the sake of one string.
 *
 * Since this includes all translations that were ever made for SMF 2.0, the
 * odds are good that no new translations will ever be submitted for this mod.
 */
function tt_editortxt()
{
	global $editortxt, $user_info, $language;

	// This is all the translated versions of the string from SMF 2.0.
	$strings = array(
		'albanian' => 'Teleshkrues',
		'arabic' => 'آلة كاتبة',
		'bulgarian' => 'Теле-шрифт',
		'catalan' => 'Teletip',
		'chinese_simplified' => '打字机文字',
		'chinese_traditional' => '打字機文字',
		'croatian' => 'Teleks',
		'czech' => 'Neproporcionální písmo',
		'czech_informal' => 'Neproporcionální písmo',
		'english' => 'Teletype',
		'estonian' => 'Teletaip',
		'finnish' => 'Tasaleveys',
		'french' => 'Télétypé',
		'german' => 'Schreibmaschine',
		'german_informal' => 'Schreibmaschine',
		'greek' => 'Γραφομηχανή',
		'hebrew' => 'מכונת כתיבה',
		'hungarian' => 'Távíró',
		'italian' => 'Testo in stile macchina da scrivere',
		'japanese' => '等幅',
		'lithuanian' => 'Teletaipas',
		'macedonian' => 'Фонт со фиксна ширина',
		'malay' => 'Jenis Talian',
		'norwegian' => 'Skrivemaskin',
		'persian' => 'تله تایپ',
		'polish' => 'Dalekopis',
		'portuguese_brazilian' => 'Teletipo',
		'portuguese_pt' => 'Teletipo',
		'romanian' => 'Telex',
		'russian' => 'Телетайп',
		'serbian_cyrillic' => 'Куцаћа машина',
		'serbian_latin' => 'Kucaća mašina',
		'spanish_es' => 'Teletipo',
		'spanish_latin' => 'Teletipo',
		'swedish' => 'Skrivmaskinsstil',
		'thai' => 'ตัวพิมพ์ดีด',
		'turkish' => 'Daktilo Tarzı Yazı',
		'ukrainian' => 'Телетайп',
		'vietnamese' => 'Dạng điện tín',
	);

	$lang = isset($user_info['language']) ? $user_info['language'] : $language;

	$editortxt['tt'] = isset($strings[$lang]) ? $strings[$lang] : $strings['english'];
}

/**
 * Mention who made this thing
 *
 * Called by:
 *      integrate_credits
 */
function tt_credits()
{
	global $context;
	$context['copyrights']['mods'][] = 'TeleTypeBBC by Jon &quot;Sesquipedalian&quot; Stovell &copy; 2023';
}
