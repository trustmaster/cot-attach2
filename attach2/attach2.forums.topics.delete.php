<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.rights
[END_COT_EXT]
==================== */

if (cot_auth('plug', 'attach2', 'W') && $usr['isadmin'] && !empty($q) && $a == 'delete')
{
	cot_check_xg();
	require_once cot_incfile('attach2', 'plug');

	foreach ($db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ?", array($q))->fetchAll(PDO::FETCH_COLUMN) as $att_post)
	{
		att_remove_all(null, 'forums', $att_post);
	}
}
