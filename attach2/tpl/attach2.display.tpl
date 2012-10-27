<!-- BEGIN: MAIN -->
<table class="cells">
	<!-- BEGIN: ATTACH_ROW -->
	<tr>
		<td class="centerall width5">#{ATTACH_ROW_NUM}</td>
		<!-- IF {ATTACH_ROW_IMG} -->
		<td class="centerall width25">
			<a href="{ATTACH_ROW_URL}" title="{ATTACH_ROW_TITLE}" ><img src="{ATTACH_ROW_ID|att_thumb($this,64,64)}" alt="{ATTACH_ROW_FILENAME}" /></a>
		</td>
		<td class="width70">
			{ATTACH_ROW_TITLE}
		</td>
		<!-- ELSE -->
		<td class="centerall width25">
			<img src="{ATTACH_ROW_EXT|att_icon($this,48)}" alt="{ATTACH_ROW_EXT}" width="48" height="48" />
		</td>
		<td class="width70">
			<p><a href="{ATTACH_ROW_URL}" title="{ATTACH_ROW_TITLE}">{ATTACH_ROW_FILENAME}</a></p>
			<p class="small">{ATTACH_ROW_SIZE} ({PHP.L.att_downloads}: {ATTACH_ROW_COUNT})</p>
		</td>
		<!-- ENDIF -->
	</tr>
<!-- END: ATTACH_ROW -->
</table>
<!-- END: MAIN -->
