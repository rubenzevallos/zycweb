<?php

	$themes = TBGContext::getThemes();
	$languages = TBGI18n::getLanguages();
	
?>
<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td><label for="defaultisguest"><?php echo __('Security key'); ?></label></td>
		<td>
			<?php echo TBGSettings::getPasswordSalt(); ?>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Whenever the "remote security key" is needed, this is the key'); ?></td>
	</tr>
	<tr>
		<td><label for="requirelogin"><?php echo __('Anonymous access'); ?></label></td>
		<td>
			<select name="requirelogin" id="requirelogin" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('You need a valid user account to access any content'); ?></option>
				<option value=0<?php if (!TBGSettings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('Use the guest user account'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="defaultisguest"><?php echo __('Guest user is authenticated'); ?></label></td>
		<td>
			<select name="defaultisguest" id="defaultisguest" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('No, the default user is a guest account'); ?></option>
				<option value=0<?php if (!TBGSettings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('Yes, the default user is a normal account'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
	</tr>
	<tr>
		<td><label for="permissive"><?php echo __('Security policy'); ?></label></td>
		<td>
			<select name="permissive" id="permissive" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isPermissive()): ?> selected<?php endif; ?>><?php echo __('Permissive'); ?></option>
				<option value=0<?php if (!TBGSettings::isPermissive()): ?> selected<?php endif; ?>><?php echo __('Restrictive'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2">
			<?php echo __("%restrictive%: With this security policy, users don't automatically get access to projects, modules, etc., but must be granted access specifically.", array('%restrictive%' => '<b>'.__('Restrictive').'</b>')); ?><br>
			<?php echo __("%permissive%: This security policy assume you have access to things like projects, pages, etc.", array('%permissive%' => '<b>'.__('Permissive').'</b>')); ?><br>
			<br>
			<?php echo __("If you're running a public tracker, or a tracker with several projects you probably want to use a restrictive security policy - however, with smaller teams or and simpler projects, permissive security policy will be most efficient."); ?><br>
			<i><?php echo __("Some permissions, such as configuration access are not affected by this setting, but must always be explicitly defined"); ?></i>
		</td>
	</tr>
	<tr>
		<td><label for="allowreg"><?php echo __('New user accounts'); ?></label></td>
		<td>
			<select name="allowreg" id="allowreg" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('Users can register new accounts'); ?></option>
				<option value=0<?php if (!TBGSettings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('All new user accounts will be created by an admin'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="limit_registration"><?php echo __('Registration domain whitelist'); ?></label></td>
		<td><input type="text" name="limit_registration" id="limit_registration"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?> value="<?php echo TBGSettings::getRegistrationDomainWhitelist(); ?>" style="width: 300px;"></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Comma-separated list of allowed domains (ex: %example%). Leave empty to allow all domains.', array('%example%' => 'thebuggenie.com, zegeniestudios.net')); ?></td>
	</tr>
	<tr>
		<td><label for="defaultgroup"><?php echo __('Default user group'); ?></label></td>
		<td>
			<select name="defaultgroup" id="defaultgroup" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (TBGGroup::getAll() as $aGroup): ?>
				<option value="<?php print $aGroup->getID(); ?>"<?php if (($default_group = TBGSettings::getDefaultGroup()) instanceof TBGGroup && $default_group->getID() == $aGroup->getID()): ?> selected<?php endif; ?>><?php print $aGroup->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('New users will automatically be added to this group'); ?></td>
	</tr>
	<tr>
		<td><label for="returnfromlogin"><?php echo __('Redirect after login'); ?></label></td>
		<td>
			<select name="returnfromlogin" id="returnfromlogin" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<?php $return_routes = array('home' => __('Frontpage'), 'dashboard' => __('Dashboard'), 'account' => __('Account details'), 'referer' => __('Page before login')); ?>
				<?php $return_routes = TBGEvent::createNew('core', 'setting_returnfromlogin', null, array(), $return_routes)->trigger()->getReturnList(); ?>
				<?php foreach ($return_routes as $route => $description): ?> 
					<option value="<?php echo $route; ?>"<?php if (TBGSettings::getLoginReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="returnfromlogout"><?php echo __('Redirect after logout'); ?></label></td>
		<td>
			<select name="returnfromlogout" id="returnfromlogout" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<?php $return_routes = array('home' => __('Frontpage')); ?>
				<?php $return_routes = TBGEvent::createNew('core', 'setting_returnfromlogout', null, array(), $return_routes)->trigger()->getReturnList(); ?>
				<?php foreach ($return_routes as $route => $description): ?> 
					<option value="<?php echo $route; ?>"<?php if (TBGSettings::getLogoutReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="onlinestate"><?php echo __('User state when online'); ?></label></td>
		<td>
			<select name="onlinestate" id="onlinestate" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (TBGUserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($onlinestate = TBGSettings::getOnlineState()) instanceof TBGDatatype && $onlinestate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="awaystate"><?php echo __('User state when inactive'); ?></label></td>
		<td>
			<select name="awaystate" id="awaystate" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (TBGUserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($awaystate = TBGSettings::getAwayState()) instanceof TBGDatatype && $awaystate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="offlinestate"><?php echo __('User state when offline'); ?></label></td>
		<td>
			<select name="offlinestate" id="offlinestate" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (TBGUserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($offlinestate = TBGSettings::getOfflineState()) instanceof TBGDatatype && $offlinestate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
</table>