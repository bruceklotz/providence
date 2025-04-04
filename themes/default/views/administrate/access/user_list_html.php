<?php
/* ----------------------------------------------------------------------
 * app/views/admin/access/user_list_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2016 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	$va_user_list = $this->getVar('user_list');

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#caItemList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="sectionBox">
<?php 
		print caFormTag($this->request, 'ListUsers', 'caUserListForm', null, 'post', 'multipart/form-data', '_top', array('noCSRFToken' => true, 'disableUnsavedChangesWarning' => true));
		print caFormControlBox(
			'<div class="list-filter">'._t('Filter').': <input type="text" name="filter" value="" onkeyup="$(\'#caItemList\').caFilterTable(this.value); return false;" size="20"/></div>', 
			''._t('Show %1 users', caHTMLSelect('userclass', $this->request->user->getFieldInfo('userclass', 'BOUNDS_CHOICE_LIST'), array('onchange' => 'jQuery("#caUserListForm").submit();'), array('value' => $this->getVar('userclass')))), 
			caNavHeaderButton($this->request, __CA_NAV_ICON_ADD__, _t("New user"), 'administrate/access', 'Users', 'Edit', array('user_id' => 0), [], ['size' => '30px'])
		); 
?>		
		<h1 style='float:left; margin:10px 0px 10px 0px;'><?= _t('%1 users', ucfirst($this->getVar('userclass_displayname'))); ?></h1>
<?php
	if(sizeof($va_user_list)){	
?>	
		<a href='#' id='showTools' style="float:left;margin-top:10px;" onclick='jQuery("#searchToolsBox").slideDown(250); jQuery("#showTools").hide(); return false;'><?= caNavIcon(__CA_NAV_ICON_SETTINGS__, "24px");?></a>
<?php
		print $this->render('user_tools_html.php');
	}
?>
		<table id="caItemList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
			<thead>
				<tr>
					<th class="list-header-unsorted">
						<?= _t('Login name'); ?>
					</th>
					<th class="list-header-unsorted">
						<?= _t('Name'); ?>
					</th>
					<th class="list-header-unsorted">
						<?= _t('Email'); ?>
					</th>
					<th class="list-header-unsorted">
						<?= _t('Active?'); ?>
					</th>
					<th class="list-header-unsorted">
						<?= _t('Last login'); ?>
					</th>
					<th class="{sorter: false} list-header-nosort listtableEditDelete"></th>
				</tr>
			</thead>
			<tbody>
<?php
	$o_tep = new TimeExpressionParser();
	foreach($va_user_list as $va_user) {
		if ($va_user['last_login'] > 0) {
			$o_tep->setUnixTimestamps($va_user['last_login'], $va_user['last_login']);
		}
?>
			<tr>
				<td>
					<?= $va_user['user_name']; ?>
				</td>
				<td>
					<?= $va_user['lname'].', '.$va_user['fname']; ?>
				</td>
				<td>
					<?= $va_user['email']; ?>
				</td>
				<td>
					<?= $va_user['active'] ? _t('Yes') : _t('No'); ?>
				</td>
				<td>
					<?= ($va_user['last_login'] > 0) ? "<span style='display:none;'>".$va_user['last_login']."</span>".$o_tep->getText() : '-'; ?>
				</td>
				<td class="listtableEditDelete">
					<?= caNavButton($this->request, __CA_NAV_ICON_EDIT__, _t("Edit"), '', 'administrate/access', 'Users', 'Edit', array('user_id' => $va_user['user_id']), array(), array('icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)); ?>
					<?= caNavButton($this->request, __CA_NAV_ICON_DELETE__, _t("Delete"), '', 'administrate/access', 'Users', 'Delete', array('user_id' => $va_user['user_id']), array(), array('icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)); ?>
				</td>
			</tr>
<?php
		TooltipManager::add('.deleteIcon', _t("Delete"));
		TooltipManager::add('.editIcon', _t("Edit"));
		TooltipManager::add('#showTools', _t("Tools"));
	}
?>
			</tbody>
		</table>
	</form>
</div>
	<div class="editorBottomPadding"><!-- empty --></div>
