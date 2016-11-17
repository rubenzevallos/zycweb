<?php if ($issue->isEditable()): ?>
	<?php if ($issue->canEditIssue()): ?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="affected_add_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_add_item', 'issue_id' => $issue->getID())); ?>');" value="<?php echo __('Add an item'); ?>" value="<?php echo __('Add an item'); ?>"></td></tr></table>
	<?php else: ?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="affected_add_button"><tr><td class="nice_button disabled" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="failedMessage('<?php echo __('You are not allowed to add an item to this list'); ?>');" value="<?php echo __('Add an item'); ?>"></td></tr></table>
	<?php endif; ?>
	<br>
<?php endif; ?>
<br>
<?php
	$editions = array();
	$components = array();
	$builds = array();
	$statuses = array();
	
	if($issue->getProject()->isEditionsEnabled())
	{
		$editions = $issue->getEditions();
	}
	
	if($issue->getProject()->isComponentsEnabled())
	{
		$components = $issue->getComponents();
	}

	if($issue->getProject()->isBuildsEnabled())
	{
		$builds = $issue->getBuilds();
	}
	
	$statuses = TBGStatus::getAll();
	
	$count = count($editions) + count($components) + count($builds);
?>	

<table style="width: 100%;" cellpadding="0" cellspacing="0" class="issue_affects" id="affected_list">
	<tr>
		<th style="width: 16px; text-align: right; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 3px;"></th><th><?php echo __('Name'); ?></th><th><?php echo __('Status'); ?></th><th style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php echo __('Confirmed'); ?></th>
	</tr>
	<?php
		
	?>
	<tr id="no_affected" <?php if ($count != 0): ?>style="display: none;"<?php endif; ?>><td colspan="4"><span class="faded_out"><?php echo __('There are no items'); ?></span></td></tr>
	<?php
		if ($issue->getProject()->isEditionsEnabled()):
			foreach ($issue->getEditions() as $edition):
				$item = $edition;
				$itemtype = 'edition';
				$itemtypename = __('Edition');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isComponentsEnabled()):
			foreach ($issue->getComponents() as $component):
				$item = $component;
				$itemtype = 'component';
				$itemtypename = __('Component');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isBuildsEnabled()):
			foreach ($issue->getBuilds() as $build):
				$item = $build;
				$itemtype = 'build';
				$itemtypename = __('Release');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
</table>
