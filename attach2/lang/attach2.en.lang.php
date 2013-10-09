<?php
/* ====================
Copyright (c) 2008-2012, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
=================== */

$L['info_desc'] = 'Attach images and files and build galleries using pages and forum posts';

$L['att_add'] = 'Add files';
$L['att_attach'] = 'Attach files';
$L['att_attachment'] = 'Attached file';
$L['att_attachments'] = 'Attachments';
$L['att_cancel'] = 'Cancel upload';
$L['att_cleanup'] = 'Clean up';
$L['att_cleanup_confirm'] = 'This will remove all files attached to posts which no longer exist. Continue?';
$L['att_delete'] = 'Delete';
$L['att_downloads'] = 'Downloads';
$L['att_ensure'] = 'Are you sure?';
$L['att_free'] = 'free';
$L['att_filename'] = 'File name';
$L['att_gallery'] = 'Gallery';
$L['att_info'] = 'Information';
$L['att_item'] = 'Item';
$L['att_items_removed'] = 'Items removed';
$L['att_kb'] = 'kB';
$L['att_kb_left_of'] = 'kB left, files not larger than';
$L['att_maxsize'] = 'max file size';
$L['att_of'] = 'of';
$L['att_remove_all'] = 'Remove all';
$L['att_replace'] = 'Replace';
$L['att_show_info'] = 'Show item details';
$L['att_size'] = 'Size';
$L['att_slideshow'] = 'Slideshow';
$L['att_start'] = 'Start';
$L['att_start_upload'] = 'Start upload';
$L['att_title'] = 'Title';
$L['att_title_here'] = 'Put the caption here';
$L['att_total'] = 'total';
$L['att_type'] = 'Type';
$L['att_used'] = 'used';
$L['att_user'] = 'User';
$L['att_your_space'] = 'Your space';

// Messages
$L['att_err_db'] = 'Database error';
$L['att_err_delete'] = 'Could not delete attachment';
$L['att_err_move'] = 'Failed to move the uploaded file';
$L['att_err_noitems'] = 'No items found';
$L['att_err_nospace'] = 'Not enough personal disk space';
$L['att_err_perms'] = 'You are not permitted to do this';
$L['att_err_replace'] = 'Could not replace file';
$L['att_err_thumb'] = 'Could not create thumbnail for image';
$L['att_err_title'] = 'File caption is empty';
$L['att_err_toobig'] = 'File is too big';
$L['att_err_type'] = 'This type of files is not allowed';
$L['att_err_upload'] = 'The file could not be uploaded';

// Configuration
$L['cfg_folder'] = 'Directory for files';
$L['cfg_prefix'] = 'File prefix';
$L['cfg_exts'] = 'Allowed extensions (comma separated, no dots and spaces)';
$L['cfg_thumbs'] = 'Display image thumbnails';
$L['cfg_thumb_x'] = 'Default thumbnail width';
$L['cfg_thumb_y'] = 'Default thumbnail height';
$L['cfg_thumb_framing'] = 'Default thumbnail framing mode';
$L['cfg_thumb_framing_params'] = array(
	'height' => 'By height',
	'width'  => 'By width',
	'auto'   => 'Auto',
	'crop'   => 'Crop'
);
$L['cfg_items'] = 'Attachments per post (max.), 0 - unlimited';
$L['cfg_upscale'] = 'Upscale images smaller than thumb size';
$L['cfg_quality'] = 'JPEG quality in %';
$L['cfg_accept'] = array('Accepted MIME types in file selection dialog, comma separated.', 'Empty means all types. You can use special types: image/*, audio/*, video/*');
$L['cfg_filesize'] = 'Max file size in bytes';
$L['cfg_filespace'] = 'Total file space per user';
$L['cfg_imageconvert'] = 'Convert all images to JPG on upload';
$L['cfg_autoupload'] = 'Start uploading automatically';
$L['cfg_sequential'] = 'Sequential uploading instead of concurrent';
