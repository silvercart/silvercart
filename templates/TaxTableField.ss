<div id="$id" class="$CSSClasses">
	<% if Print %><% else %>
		<% if Markable %>
			<% include TableListField_SelectOptions %>
		<% end_if %>
		<% include TableListField_PageControls %>
	<% end_if %>
	<table class="data">
		<thead>
			<tr>
				<% if Markable %><th width="18">&nbsp;</th><% end_if %>
				<% control Headings %>
                    <th class="$Name">
                        $Title
                    </th>
				<% end_control %>
				<% if Can(edit) %><th width="18">&nbsp;</th><% end_if %>
				<% if Can(delete) %><th width="18">&nbsp;</th><% end_if %>
			</tr>
		</thead>
		<tfoot>
			<% if HasSummary %>
			<tr class="summary">
				<% if Markable %><th width="18">&nbsp;</th><% end_if %>
				<td><i>$SummaryTitle</i></td>
				<% control SummaryFields %>
					<td<% if Function %> class="$Function"<% end_if %>>&nbsp;</td>
				<% end_control %>
				<% if Can(edit) %><th width="18">&nbsp;</th><% end_if %>
				<% if Can(delete) %><th width="18">&nbsp;</th><% end_if %>
			</tr>
			<% end_if %>
		</tfoot>
		<tbody>
			<% if Items %>
			<% control Items %>
				<tr id="record-$Parent.id-$ID"<% if HighlightClasses %> class="$HighlightClasses"<% end_if %>>
					<% if Markable %><td width="18" class="$SelectOptionClasses">$MarkingCheckbox</td><% end_if %>
					<% control Fields %>
					<td class="$Title">$Value</td>
					<% end_control %>
					<% if Can(edit) %>
						<td width="18"><a class="popuplink editlink" href="$EditLink" target="_blank"><img src="cms/images/edit.gif" alt="<% _t('EDIT', 'edit') %>" /></a></td>
					<% end_if %>
					<% if Can(delete) %>
						<td width="18"><a class="deletelink" href="$DeleteLink" title="<% _t('DELETEROW', 'Delete this row') %>"><img src="cms/images/delete.gif" alt="<% _t('DELETE', 'delete') %>" /></a></td>
					<% end_if %>
				</tr>
			<% end_control %>
			<% else %>
				<tr class="notfound">
					<% if Markable %><th width="18">&nbsp;</th><% end_if %>
					<td colspan="$Headings.Count"><i><% _t('NOITEMSFOUND', 'No items found') %></i></td>
					<% if Can(edit) %><th width="18">&nbsp;</th><% end_if %>
					<% if Can(delete) %><th width="18">&nbsp;</th><% end_if %>
				</tr>
			<% end_if %>
		</tbody>
	</table>
</div>

