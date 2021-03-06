<?php

	$tbg_response->setTitle(__('Configure modules'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php

	include_component('leftmenu', array('selected_section' => 15));

?>
<td valign="top">
	<div style="width: 750px;" id="config_modules">
		<div class="config_header"><?php echo __('Configure module "%module_name%"', array('%module_name%' => $module->getLongName())); ?></div>
		<?php if ($module_error !== null): ?>
			<div class="rounded_box red borderless" style="margin: 5px 0px 5px 0px; color: #FFF; width: 750px;" id="module_error">
				<div class="header"><?php echo $module_error; ?></div>
				<div class="content"><b><?php echo __('Error details:'); ?></b><br>
					<?php if ($module_error_details !== null): ?>
						<?php if (is_array($module_error_details)): ?>
							<?php foreach ($module_error_details as $detail): ?>
								<?php echo $detail; ?><br>
							<?php endforeach; ?>
						<?php else: ?>
							<?php echo $module_error_details; ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($module_message !== null): ?>
			<div class="rounded_box green borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_message">
				<div class="header"><?php echo $module_message; ?></div>
			</div>
		<?php endif; ?>
		<?php include_component($module->getName() . '/settings', array('access_level' => $access_level, 'module' => $module)); ?>
	</div>
</td>
</tr>
</table>