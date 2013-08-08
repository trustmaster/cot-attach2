<!-- BEGIN: MAIN -->
<link href="plugins/attach2/lib/lightzap/css/lightzap.css" rel="stylesheet" type="text/css" />
<link href="plugins/attach2/lib/lightzap/css/lz-theme.css" rel="stylesheet" type="text/css" />
<script src="plugins/attach2/lib/lightzap/js/lightzap.js"></script>
<style type="text/css">
.att-gallery { }
.att-gallery .att-item { margin:10px 20px 0 0; height:220px; width:220px; display:block; float:left; }
.att-gallery .att-item img { padding:1px; background:#fff; border:4px solid #ccc; display:block; }
</style>

<div class="att-gallery clearfix">
<!-- BEGIN: ATTACH_ROW -->
	<div class="att-item">
		<a href="{ATTACH_ROW_URL}" data-lightzap="attgal" title="{ATTACH_ROW_TITLE}" ><img src="{ATTACH_ROW_ID|att_thumb($this,200,200,'crop')}" alt="{ATTACH_ROW_FILENAME}" /></a>
	</div>
<!-- END: ATTACH_ROW -->
</div>

<!-- END: MAIN -->
