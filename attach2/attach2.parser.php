<?php defined('COT_CODE') or die('Wrong URL.');
/* ====================
[BEGIN_COT_EXT]
Hooks=parser.last
[END_COT_EXT]
==================== */

require_once cot_incfile('attach2', 'plug');

if (!function_exists('att_thumb_bbcode'))
{
	// Replaces att_thumb bbcode with the thumbnail image alone
	function att_thumb_bbcode($m)
	{
		global $db, $db_attach, $att_item_cache;

		parse_str(htmlspecialchars_decode($m[1]), $params);

		if (!isset($params['id']) || !is_numeric($params['id']) || $params['id'] <= 0)
		{
			return $m[0].'err';
		}
		$params['id'] = (int) $params['id'];
		$src = att_thumb($params['id'], $params['width'], $params['height'], $params['frame']);
		if (!$src)
		{
			return $m[0].'err2';
		}
		$html = '<img src="'.$src.'"';
		if (empty($params['alt']))
		{
			if (!isset($att_item_cache[$params['id']]))
			{
				$row = $db->query("SELECT * FROM $db_attach WHERE att_id = ?", array($params['id']))->fetch();
				if (!$row || !$row['att_img'])
				{
					return $m[0].'err';
				}
				$att_item_cache[$params['id']] = $row;
			}
			$params['alt'] = $att_item_cache[$params['id']]['att_title'];
		}
		$html .= ' alt="' . htmlspecialchars($params['alt']) . '"';
		if (!empty($params['class']))
		{
			$html .= ' class="' . $params['class'] . '"';
		}
		$html .= ' />';
		return $html;
	}

	// Replaces att_image bbcode with a thumbnail wrapped with a link to full image
	function att_image_bbcode($m)
	{
		global $db, $db_attach, $att_item_cache;

		parse_str(htmlspecialchars_decode($m[1]), $params);

		if (!isset($params['id']) || !is_numeric($params['id']) || $params['id'] <= 0)
		{
			return $m[0].'err';
		}
		$params['id'] = (int) $params['id'];

		if (!isset($att_item_cache[$params['id']]))
		{
			$row = $db->query("SELECT * FROM $db_attach WHERE att_id = ?", array($params['id']))->fetch();
			if (!$row || !$row['att_img'])
			{
				return $m[0].'err';
			}
			$att_item_cache[$params['id']] = $row;
		}
		else
		{
			$row = $att_item_cache[$params['id']];
		}

		$img = att_thumb_bbcode($m);

		$html = '<a href="' . att_path($row['att_area'], $row['att_item'], $row['att_id'], $row['att_ext']) . '" title="' . htmlspecialchars($row['att_title']) . '" rel="att_image_preview">' . $img . '</a>';
		return $html;
	}
}

$text = preg_replace_callback('`\[att_thumb\?(.+?)\]`i', 'att_thumb_bbcode', $text);
$text = preg_replace_callback('`\[att_image\?(.+?)\]`i', 'att_image_bbcode', $text);
