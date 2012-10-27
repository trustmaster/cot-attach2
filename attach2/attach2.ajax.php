<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL.');

$area = cot_import('area', 'R', 'ALP');
$item = cot_import('item', 'R', 'INT');
$id   = cot_import('id', 'G', 'INT');

$response_code = 200;

if ($a == 'upload')
{
	require_once cot_incfile('attach2', 'plug', 'upload');
}
elseif ($a == 'display')
{
	$t = new XTemplate(cot_tplfile('attach2.files', 'plug'));

	// Metadata

	$limits = att_get_limits();

	$t->assign(array(
		'ATTACH_AREA'    => $area,
		'ATTACH_ITEM'    => $item,
		'ATTACH_EXTS'    => preg_replace('#[^a-zA-Z0-9,]#', '', $cfg['plugin']['attach2']['exts']),
		'ATTACH_ACCEPT'  => preg_replace('#[^a-zA-Z0-9,*/-]#', '',$cfg['plugin']['attach2']['accept']),
		'ATTACH_MAXSIZE' => $limits['file'],
		'ATTACH_ACTION' => 'index.php?r=attach2&a=upload&area='.$area.'&item='.$item
	));

	$t->parse();
	$t->out();
	exit;
}
elseif ($a == 'dl' && $id > 0)
{
	// File download gateway
	require_once cot_incfile('attach2', 'plug', 'download');
}

if ($a == 'replace' && $id > 0 && $_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Replacing an existing attachment
	if (att_update_file($id, 'file'))
	{
		$response = array(
			'status' => 1
		);
	}
	else
	{
		$errors = cot_implode_messages();
		cot_clear_messages();
		cot_ajax_die(403, array('message' => $errors));
	}
}
elseif ($a == 'update_title' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Update attachment title via AJAX
	if ($id > 0)
	{
		$row = $db->query("SELECT * FROM $db_attach WHERE att_id = ?", array($id))->fetch();
		if (!$row)
		{
			att_ajax_die(404);
		}
		if (!$usr['isadmin'] && $row['att_user'] != $usr['id'])
		{
			att_ajax_die(403);
		}

		$title = cot_import('title', 'P', 'TXT');

		$status = 0;
		if ($title != $row['att_title'])
		{
			$status = $db->update($db_attach, array('att_title' => $title), "att_id = ?", array($id));
		}
		$response = array(
			'written' => $status
		);
	}
	else
	{
		$response_code = 404;
	}
}
elseif ($a == 'reorder' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Check permission
	if (!$usr['isadmin'] && $db->query("SELECT COUNT(*) FROM $db_attach WHERE att_area = ? AND att_item = ? AND att_user = ?", array($area, $item, $usr['id']))->fetchColumn() == 0)
	{
		att_ajax_die(403);
	}

	$orders = cot_import('orders', 'P', 'ARR');

	foreach ($orders as $order => $id)
	{
		$db->update($db_attach, array('att_order' => $order), "att_id = ? AND att_area = ? AND att_item = ? AND att_order != ?", array((int)$id, $area, $item, $order));
	}

	$response = array(
		'status' => 1
	);

}

cot_sendheaders('application/json', att_ajax_get_status($response_code));

if (!is_null($response))
	echo json_encode($response);

/**
 * Terminates further script execution with a given
 * HTTP response status and output.
 * If the message is omitted, then it is taken from the
 * HTTP status line.
 * @param  int    $code     HTTP/1.1 status code
 * @param  string $message  Output string
 * @param  array  $response Custom response object
 */
function att_ajax_die($code, $message = null, $response = null)
{
	$status = att_ajax_get_status($code);
	cot_sendheaders('application/json', $status);
	if (is_null($message))
	{
		$message = substr($status, strpos($status, ' ') + 1);
	}
	if (is_null($response))
		echo json_encode($message);
	else
	{
		$response['message'] = $message;
		echo json_encode($response);
	}
	exit;
}

/**
 * Returns HTTP satus line for a given
 * HTTP response code
 * @param  int    $code HTTP response code
 * @return string       HTTP status line
 */
function att_ajax_get_status($code)
{
	static $msg_status = array(
		200 => '200 OK',
		201 => '201 Created',
		204 => '204 No Content',
		205 => '205 Reset Content',
		206 => '206 Partial Content',
		300 => '300 Multiple Choices',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		303 => '303 See Other',
		304 => '304 Not Modified',
		307 => '307 Temporary Redirect',
		400 => '400 Bad Request',
		401 => '401 Authorization Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		409 => '409 Conflict',
		411 => '411 Length Required',
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
		503 => '503 Service Unavailable',
	);
	if (isset($msg_status[$code]))
		return $msg_status[$code];
	else
		return "$code Unknown";
}
