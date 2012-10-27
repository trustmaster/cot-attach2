<!-- BEGIN: MAIN -->
<style type="text/css">
.att-downloads { border:0; }
.att-downloads .att-icon { width:32px;padding:4px 8px 8px 4px; }
.att-downloads .att-fileinfo { text-align: left;padding:4px 8px 4px 8px;}
</style>
<table class="att-downloads">
	<!-- BEGIN: ATTACH_ROW -->
	<tr>
		<td class="att-icon">
			<a href="{ATTACH_ROW_URL}" title="{ATTACH_ROW_TITLE}">
				<img src="{ATTACH_ROW_EXT|att_icon($this,32)}" alt="{ATTACH_ROW_EXT}" width="32" height="32" />
			</a>
		</td>
		<td class="att-fileinfo">
			<h4><a href="{ATTACH_ROW_URL}" title="{ATTACH_ROW_TITLE}">{ATTACH_ROW_FILENAME}</a></h4>
			<p class="small">{ATTACH_ROW_SIZE} ({PHP.L.att_downloads}: {ATTACH_ROW_COUNT})</p>
		</td>
	</tr>
	<!-- END: ATTACH_ROW -->
</table>
<!-- END: MAIN -->
