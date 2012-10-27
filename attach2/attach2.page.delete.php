<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
[END_COT_EXT]
==================== */

if (cot_auth('plug', 'attach2', 'W'))
{
	require_once cot_incfile('attach2', 'plug');

	att_remove_all(null, 'page', $id);
}
