<?php TBGContext::loadLibrary('ui'); ?>
<div class="rounded_box round_canhover lightgrey projectbox" style="margin: 10px 0px 10px 0px; width: 690px;" id="project_box_<?php echo $project->getID();?>">
	<div style="padding: 3px; font-size: 14px;">
		<strong><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?></strong>&nbsp;(<?php echo $project->getKey(); ?>)
		<?php if ($project->usePrefix()): ?>
			&nbsp;-&nbsp;<i><?php echo $project->getPrefix(); ?></i>
		<?php endif; ?>
	</div>
	<table cellpadding=0 cellspacing=0 style="width: 680px; table-layout: auto;">
	<tr>
	<td style="padding-left: 3px; width: 80px;"><b><?php echo __('Owner: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
	<td style="padding-left: 3px; width: auto;">
		<?php if ($project->getOwner() != null): ?>
			<?php if ($project->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
				<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
			<?php elseif ($project->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM): ?>
				<?php echo include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
			<?php endif; ?>
		<?php else: ?>
			<div style="color: #AAA; padding: 2px; width: auto;"><?php echo __('None'); ?></div>
		<?php endif; ?>
	</td>
	</tr>
	<?php if ($project->hasDescription()): ?>
		<tr>
			<td colspan="2" style="padding: 3px;"><?php echo tbg_parse_text($project->getDescription()); ?></td>
		</tr>
	<?php endif; ?>
	<tr>
		<td colspan="2" style="border-top: 1px solid #DDD; padding: 5px; background-color: transparent;">
		<?php if (!$project->isEditionsEnabled() && $project->isBuildsEnabled()): ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><strong><?php echo javascript_link_tag(image_tag('cfg_icon_builds.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'section' => 'hierarchy', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></strong></span>
			</div>
		<?php endif; ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><strong><?php echo javascript_link_tag(image_tag('cfg_icon_projectsettings.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></strong></span>
			</div>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<div style="float: right;"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').show();"><?php echo image_tag('icon_delete.png', array('title' => __('Delete project'), 'style' => 'float: left; margin-right: 5px;')) . __('Delete');?></a></span></div>
			<?php endif; ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), array('onclick' => "$('project_{$project->getID()}_permissions').toggle();", 'style' => 'font-size: 12px;')); ?></span>
			</div>
			<br style="clear: both;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<div id="project_delete_confirm_<?php echo($project->getID()); ?>" style="display: none; padding: 0 10px 5px 10px; margin-top: 5px;" class="rounded_box white shadowed">
					<h4><?php echo __('Really delete project?'); ?></h4>
					<span class="question_header"><?php echo __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?></span><br>
					<div style="text-align: right;" id="project_delete_controls_<?php echo($project->getID()); ?>"><a href="javascript:void(0)" class="xboxlink" onClick="removeProject('<?php echo make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>)"><?php echo __('Yes'); ?></a> :: <a href="javascript:void(0)" class="xboxlink" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').hide();"><?php echo __('No'); ?></a></div>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="project_delete_indicator_<?php echo($project->getID()); ?>">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Deleting project, please wait'); ?>...</td>
						</tr>
					</table>
					<div id="project_delete_error_<?php echo($project->getID()); ?>" style="display: none;"><b><?php echo __('System error when deleting project'); ?></b></div>
				</div>
			<?php endif; ?>
		<?php if ($project->hasEditions() && $project->isEditionsEnabled()): ?>
			<br style="clear: both;">
			<div class="config_header"><b><?php echo __('Project editions'); ?></b></div>
			<table cellpadding=0 cellspacing=0 style="width: 670px; table-layout: auto;">
			<?php foreach ($project->getEditions() as $edition): ?>
				<tr class="hover_highlight">
					<td style="width: auto; padding: 3px 0 3px 5px;">
						<div style="float: right;"><span style="margin-right: 10px;"><?php echo javascript_link_tag(image_tag('cfg_icon_builds.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'edition_id' => $edition->getID(), 'section' => 'releases', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></span></div>
						<div style="float: right;"><span style="margin-right: 20px;"><?php echo javascript_link_tag(image_tag('cfg_icon_editiondetails.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit details') : __('Show details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit details') : __('Show details')), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'edition_id' => $edition->getID(), 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></span></div>
						<?php echo $edition->getName(); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</table>
		<?php endif; ?>
		</td>
	</tr>
	</table>
	<div class="rounded_box white shadowed config_permissions" id="project_<?php echo $project->getID(); ?>_permissions" style="display: none;">
		<?php include_template('configuration/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
</div>
<?php if (TBGContext::getRequest()->isAjaxCall()): ?>
	<script type="text/javascript">new Effect.Pulsate('project_box_<?php echo $project->getID(); ?>');</script>
<?php endif; ?>
