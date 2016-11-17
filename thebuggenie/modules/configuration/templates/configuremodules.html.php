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
		<div class="config_header"><?php echo __('Configure modules'); ?></div>
		<div class="header"><?php echo __('Installed modules'); ?></div>
		<div class="content"><?php echo __('This is a list of all modules that are installed on this system'); ?></div>
		<?php if ($module_error !== null): ?>
			<div class="rounded_box red borderless" style="margin: 5px 0px 5px 0px; color: #FFF; width: 750px;" id="module_error">
				<div class="header"><?php echo $module_error; ?></div>
			</div>
		<?php endif; ?>
		<?php if ($module_message !== null): ?>
			<div class="rounded_box green borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_message">
				<div class="header"><?php echo $module_message; ?></div>
			</div>
		<?php endif; ?>
		<?php foreach ($modules as $module_key => $module): ?>
			<?php include_template('modulebox', array('module' => $module)); ?>
		<?php endforeach; ?>
		<div class="header" style="margin-top: 15px;"><?php echo __('Uninstalled modules'); ?></div>
		<?php if (count($uninstalled_modules) == 0): ?>
			<div class="faded_out" style="margin-top: 5px;"><?php echo __('There are no uninstalled modules available'); ?></div>
		<?php else: ?>
			<form action="<?php echo make_url('configure_install_module'); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
				<div class="rounded_box mediumgrey borderless" style="margin: 5px 0px 5px 0px; text-align: right; width: 750px;">
					<div class="content">
						<?php echo __('This is a list of modules available, but not installed on this system.'); ?>
						<?php echo __('To install a module, select it from the dropdown list and press the %install%-button', array('%install%' => '<b>' . __('Install') . '</b>')); ?>
					</div>
					<select name="module_key" style="margin-top: 5px; width: 100%;">
					<?php foreach ($uninstalled_modules as $module_key => $description): ?>
						<option value="<?php echo $module_key; ?>"><?php echo $description; ?></option>
					<?php endforeach; ?>
					</select><br>
					<input type="submit" value="<?php echo __('Install'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
				</div>
			</form>
		<?php endif; ?>
	</div>
</td>
</tr>
</table>