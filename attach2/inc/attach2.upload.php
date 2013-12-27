<?php defined('COT_CODE') or die('Wrong URL');

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

$filename = cot_import('file', 'R', 'TXT');
if (!is_null($filename))
{
	$filename = mb_basename(stripslashes($filename));
}

switch ($_SERVER['REQUEST_METHOD'])
{
	case 'OPTIONS':
		break;
	case 'HEAD':
	case 'GET':
		header('Content-type: application/json');
		echo json_encode(att_ajax_get($area, $item, $filename));
		break;
	case 'POST':
		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE')
		{
			// Attachment removal for servers not supporting DELETE
			$id = cot_import('id', 'R', 'INT');
			header('Content-type: application/json');
			if ($id > 0)
			{
				echo json_encode(array('success' => (bool) att_remove($id)));
			}
			else
			{
				echo json_encode(array('success' => false));
			}
		}
		else
		{
			echo att_ajax_post();
		}
		break;
	default:
		header('HTTP/1.1 405 Method Not Allowed');
}

/**
 * Fetches AJAX data for a given file or all files attached
 * @param  string  $area     Target module/plugin code
 * @param  int     $item     Target item id
 * @param  string  $filename Name of the original file
 * @return array             Data for JSON response
 */
function att_ajax_get($area, $item, $filename = null)
{
	global $cfg, $db, $db_attach, $sys;

	if (is_null($filename) || empty($filename))
	{
		$multi = true;
		$res = $db->query("SELECT * FROM $db_attach
			WHERE att_area = ? AND att_item = ? ORDER BY att_order",
			array($area, (int)$item));
	}
	else
	{
		$multi = false;
		$res = $db->query("SELECT * FROM $db_attach
			WHERE att_area = ? AND att_item = ? AND att_filename = ? LIMIT 1",
			array($area, (int)$item, $filename));
	}

	if ($res->rowCount() == 0)
	{
		return null;
	}

	$files = array();

	foreach ($res->fetchAll() as $row)
	{
		$file = array(
			'id'          => $row['att_id'],
			'name'        => $row['att_filename'],
			'size'        => (int) $row['att_size'],
			'url'         => $cfg['mainurl'] . '/' . att_path($area, $item, $row['att_id'], $row['att_ext']),
			'delete_type' => 'POST',
			'delete_url'  => $cfg['mainurl'] . '/index.php?r=attach2&a=upload&id='.$row['att_id'].'&_method=DELETE&x='.$sys['xk'],
			'title'       => htmlspecialchars($row['att_title']),
			'lastmod'     => $row['att_lastmod']
		);

		if ($row['att_img'])
		{
			$file['thumbnail_url'] = $cfg['mainurl'] . '/' . att_thumb($row['att_id']) . '?lastmod=' . $row['att_lastmod'];
		}
		else
		{
			$file['thumbnail_url'] = $cfg['mainurl'] . '/' . att_icon(att_get_ext($row['att_filename']));
		}

		if (!$multi)
		{
			return $file;
		}
		else
		{
			$files[] = $file;
		}
	}

	return array('files' => $files);
}

/**
 * Handles POST file uploads.
 * @return string         JSON response
 */
function att_ajax_post()
{
	$param_name = 'files';
	$upload = isset($param_name) ? $_FILES[$param_name] : null;
	$info = array();

	if ($upload && is_array($upload['tmp_name']))
	{
		// param_name is an array identifier like "files[]",
		// $_FILES is a multi-dimensional array:
		foreach (array_keys($upload['tmp_name']) as $index)
		{
			$info[] = att_ajax_handle_file_upload(
				$upload['tmp_name'][$index],
				isset($_SERVER['HTTP_X_FILE_NAME']) ?
					$_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
				isset($_SERVER['HTTP_X_FILE_SIZE']) ?
					$_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
				isset($_SERVER['HTTP_X_FILE_TYPE']) ?
					$_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
				$upload['error'][$index],
				$index
			);
		}
	}
	elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME']))
	{
		// param_name is a single object identifier like "file",
		// $_FILES is a one-dimensional array:
		$info[] = att_ajax_handle_file_upload(
			isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
			isset($_SERVER['HTTP_X_FILE_NAME']) ?
				$_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
					$upload['name'] : null),
			isset($_SERVER['HTTP_X_FILE_SIZE']) ?
				$_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
					$upload['size'] : null),
			isset($_SERVER['HTTP_X_FILE_TYPE']) ?
				$_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
					$upload['type'] : null),
			isset($upload['error']) ? $upload['error'] : null
		);
	}
	header('Vary: Accept');
	$json = json_encode(array('files' => $info));
	$redirect = isset($_REQUEST['redirect']) ?
		stripslashes($_REQUEST['redirect']) : null;
	if ($redirect)
	{
		header('Location: '.sprintf($redirect, rawurlencode($json)));
		return;
	}
	if (isset($_SERVER['HTTP_ACCEPT']) &&
		(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
		header('Content-type: application/json');
	} else {
		header('Content-type: text/plain');
	}
	return $json;
}

// AJAX upload handler
function att_ajax_handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null)
{
	global $area, $item, $cfg, $db, $db_attach, $usr, $L, $sys;

	$file = new stdClass();
	$file->name = trim(mb_basename(stripslashes($name)));
	$file->size = intval($size);
	$file->type = $type;
	if (att_ajax_validate($uploaded_file, $file, $error, $index))
	{
		// Handle form data, e.g. $_REQUEST['description'][$index]
		//$this->handle_form_data($file, $index);

		// First create a database entry because we need an ID for filename
		$file_ext = att_get_ext($file->name);
		$is_img = (int)in_array($file_ext, array('gif', 'jpg', 'jpeg', 'png'));

		$order = ((int)$db->query("SELECT MAX(att_order) FROM $db_attach WHERE att_area = ? AND att_item = ?", array($area, $item))->fetchColumn()) + 1;

		$affected = $db->insert($db_attach, array(
			'att_user'     => $usr['id'],
			'att_area'     => $area,
			'att_item'     => $item,
			'att_path'     => '',
			'att_filename' => $file->name,
			'att_ext'      => $file_ext,
			'att_img'      => $is_img,
			'att_size'     => $file->size,
			'att_title'    => '',
			'att_count'    => 0,
			'att_order'    => $order,
			'att_lastmod'  => $sys['now']
		));

		if ($affected != 1)
		{
			$file->error = $L['att_err_db'];
			return $file;
		}

		$id = $db->lastInsertId();
		$file_path = att_path($area, $item, $id, $file_ext);

		$dir_path = dirname($file_path);
		if (!file_exists($dir_path))
		{
			mkdir($dir_path, $cfg['dir_perms'], true);
		}

		clearstatcache();
		if ($uploaded_file && is_uploaded_file($uploaded_file))
		{
			// multipart/formdata uploads (POST method uploads)
			move_uploaded_file($uploaded_file, $file_path);
		}
		else
		{
			// Non-multipart uploads (PUT method support)
			file_put_contents(
				$file_path,
				fopen('php://input', 'r'),
				0
			);
		}
		$file_size = filesize($file_path);
		if ($file_size === $file->size)
		{
			// Automatic JPG conversion feature
			if ($cfg['plugin']['attach2']['imageconvert'] && $is_img && $file_ext != 'jpg' && $file_ext != 'jpeg')
			{
				$input_file = $file_path;
				$output_file = att_path($area, $item, $id, 'jpg');
				if ($file_ext == 'png')
					$input = imagecreatefrompng($input_file);
				else
					$input = imagecreatefromgif($input_file);
				list($width, $height) = getimagesize($input_file);
				$output = imagecreatetruecolor($width, $height);
				$white = imagecolorallocate($output,  255, 255, 255);
				imagefilledrectangle($output, 0, 0, $width, $height, $white);
				imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
				imagejpeg($output, $output_file);
				$file_path = $output_file;
				$file_size = filesize($output_file);
				$file_ext = 'jpg';
				$file->name = pathinfo($file->name, PATHINFO_FILENAME) . '.jpg';
			}
			if ($is_img)
			{
				// Fix image orientation via EXIF if possible
				if (function_exists('exif_read_data'))
				{
					$exif = exif_read_data($file_path);
					cot_watch($exif);
					if (isset($exif['Orientation']) && !empty($exif['Orientation']) && in_array($exif['Orientation'], array(3, 6, 8)))
					{
						cot_watch($exif['Orientation']);
						switch ($ext)
						{
							case 'gif':
								$newimage = imagecreatefromgif($file_path);
								break;
							case 'png':
								imagealphablending($newimage, false);
								imagesavealpha($newimage, true);
								$newimage = imagecreatefrompng($file_path);
								break;
							default:
								$newimage = imagecreatefromjpeg($file_path);
								break;
						}
						switch ($exif['Orientation'])
						{
							case 3:
								$newimage = imagerotate($newimage, 180, 0);
								break;
							case 6:
								$newimage = imagerotate($newimage, -90, 0);
								break;
							case 8:
								$newimage = imagerotate($newimage, 90, 0);
								break;
						}
						switch ($ext)
						{
							case 'gif':
								imagegif($newimage, $file_path);
								break;
							case 'png':
								imagepng($newimage, $file_path);
								break;
							default:
								imagejpeg($newimage, $file_path, 96);
								break;
						}
						cot_watch('Saved');
					}
				}
			}
			$db->update($db_attach, array(
				'att_path'     => $file_path,
				'att_size'     => $file_size,
				'att_ext'      => $file_ext,
				'att_filename' => $file->name
			), "att_id = $id");
			$file->url = $cfg['mainurl'] . '/' . $file_path;
			$file->thumbnail_url = ($is_img) ? $cfg['mainurl'] . '/' . att_thumb($id) : $cfg['mainurl'] . '/' . att_icon($file_ext);
			$file->id = $id;
		}
		else
		{
			unlink($file_path);
			// Recover db state
			$db->delete($db_attach, "att_id = $id");
			$file->error = 'abort';
			return $file;
		}
		$file->size = $file_size;
	}
	return $file;
}

// Validates uploaded file
function att_ajax_validate($uploaded_file, $file, $error)
{
	global $area, $item, $L;

	if(!cot_auth('plug', 'attach2', 'W'))
	{
		$file->error = $L['att_err_perms'];
		return false;
	}

	if ($error) {
		$file->error = $error;
		return false;
	}
	if (!$file->name) {
		$file->error = 'missingFileName';
		return false;
	}

	$file_ext = att_get_ext($file->name);
	if (!att_check_file($file_ext)) {
		$file->error = 'acceptFileTypes';
		return false;
	}

	if ($uploaded_file && is_uploaded_file($uploaded_file)) {
		$file_size = filesize($uploaded_file);
	} else {
		$file_size = $_SERVER['CONTENT_LENGTH'];
	}

	$limits = att_get_limits();
	if ($limits['file'] && (
			$file_size > $limits['file'] ||
			$file->size > $limits['file'])
		) {
		$file->error = 'maxFileSize';
		return false;
	}
	if (1 &&
		$file_size < 1) {
		$file->error = 'minFileSize';
		return false;
	}
	if ($cfg['plugin']['attach2']['items'] > 0 && (
			att_count_files($area, $item) >= $cfg['plugin']['attach2']['items'])
		) {
		$file->error = 'maxNumberOfFiles';
		return false;
	}
	// list($img_width, $img_height) = @getimagesize($uploaded_file);
	// if (is_int($img_width)) {
	// 	if ($this->options['max_width'] && $img_width > $this->options['max_width'] ||
	// 			$this->options['max_height'] && $img_height > $this->options['max_height']) {
	// 		$file->error = 'maxResolution';
	// 		return false;
	// 	}
	// 	if ($this->options['min_width'] && $img_width < $this->options['min_width'] ||
	// 			$this->options['min_height'] && $img_height < $this->options['min_height']) {
	// 		$file->error = 'minResolution';
	// 		return false;
	// 	}
	// }
	return true;
}

// workaround for splitting basename whith beginning utf8 multibyte char
function mb_basename($filepath, $suffix = NULL)
{
	$splited = preg_split('/\//', rtrim($filepath, '/ '));
	return substr(basename('X' . $splited[count($splited) - 1], $suffix), 1);
}
