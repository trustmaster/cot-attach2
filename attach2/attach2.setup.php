<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
Copyright (c) 2008-2013, Vladimir Sibirov, Skuola.net and Seditio.By.
All rights reserved. Distributed under BSD License.

[BEGIN_COT_EXT]
Code=attach2
Name=Attachments
Category=files-media
Description=Attach files to posts and pages
Version=2.1.6
Date=2013-12-27
Author=Trustmaster
Copyright=(c) Vladimir Sibirov, Skuola.net and Seditio.By, 2008-2013
Notes=DO NOT FORGET to create a writable folder for attachments
SQL=
Auth_guests=R1
Lock_guests=2345A
Auth_members=RW1
Lock_members=2345
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
folder=01:string::datas/attach:Directory for files
prefix=02:string::att_:File prefix
exts=03:text::gif,jpg,jpeg,png,zip,rar,7z,gz,bz2,pdf,djvu,mp3,ogg,wma,avi,divx,mpg,mpeg,swf,txt:Allowed extensions (comma separated, no dots and spaces)
thumbs=04:radio::1:Display image thumbnails
thumb_x=05:string::160:Default thumbnail width
thumb_y=06:string::160:Default thumbnail height
thumb_framing=06:select:height,width,auto,crop:auto:Default thumbnail framing mode
items=07:string::8:Attachments per post (max.), 0 - unlimited
upscale=08:radio::0:Upscale images smaller than thumb size
quality=09:string::85:JPEG quality in %
accept=10:text:::Accepted MIME types in file selection dialog, comma separated. Empty means all types.
filesize=11:string::4194304:Max file size in bytes
filespace=12:string::104857600:Total file space per user
autoupload=21:radio::0:Start uploading automatically
sequential=22:radio::0:Sequential uploading instead of concurrent
imageconvert=41:radio::0:Convert all images to JPG on upload
[END_COT_EXT_CONFIG]
==================== */
