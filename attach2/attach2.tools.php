<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

require_once cot_incfile('attach2', 'plug');

if ($a == 'cleanup')
{
	$count = 0;
	if (cot_module_active('forums'))
	{
		// Remove unused forum attachments
		require_once cot_incfile('forums', 'module');

		$condition = "LEFT JOIN $db_forum_posts ON $db_attach.att_item = $db_forum_posts.fp_id
		WHERE $db_attach.att_area = 'forums' AND $db_forum_posts.fp_id IS NULL";

		$res = $db->query("SELECT att_id FROM $db_attach $condition");
		$count += $res->rowCount();
		foreach ($res->fetchAll(PDO::FETCH_COLUMN) as $att_id)
		{
			att_remove($att_id);
		}
	}

	if (cot_module_active('page'))
	{
		// Remove unused page attachments
		require_once cot_incfile('page', 'module');

		$condition = "LEFT JOIN $db_pages ON $db_attach.att_item = $db_pages.page_id
		WHERE $db_attach.att_area = 'page' AND $db_pages.page_id IS NULL";

		$res = $db->query("SELECT att_id FROM $db_attach $condition");
		$count += $res->rowCount();
		foreach ($res->fetchAll(PDO::FETCH_COLUMN) as $att_id)
		{
			att_remove($att_id);
		}
	}

	cot_message($count . ' ' . $L['att_items_removed']);

	// Return to the main page and show messages
	cot_redirect(cot_url('admin', 'm=other&p=attach2', '', true));
}

$tt = new XTemplate(cot_tplfile('attach2.tools', 'plug'));

cot_display_messages($tt);

$tt->parse();
$plugin_body = $tt->text('MAIN');
