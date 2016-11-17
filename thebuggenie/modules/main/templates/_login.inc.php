<div class="rounded_box white borderless shadowed backdrop_box large"  id="login_popup">
	<div class="backdrop_detail_content rounded_top" id="login_content">
		<div class="tab_menu">
			<ul id="login_menu">
				<li id="tab_login"<?php if ($selected_tab == 'login'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_login.png', array('style' => 'float: left;')).__('Login'), array('onclick' => "switchSubmenuTab('tab_login', 'login_menu');")); ?></li>
				<?php TBGEvent::createNew('core', 'login_form_tab')->trigger(array('selected_tab' => $selected_tab)); ?>
				<?php if (TBGSettings::get('allowreg') == true): ?>
					<li id="tab_register"<?php if ($selected_tab == 'register'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_register.png', array('style' => 'float: left;')).__('Register new account'), array('onclick' => "switchSubmenuTab('tab_register', 'login_menu');")); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div id="login_menu_panes">
			<div id="tab_login_pane"<?php if ($selected_tab != 'login'): ?> style="display: none;"<?php endif; ?>>
				<script language="text/javascript">
					if (document.location.href.search('<?php echo make_url('login_redirect'); ?>') != -1)
					{
						$('tbg3_referer').setValue('<?php echo make_url('dashboard'); ?>');
					}
					else
					{
						$('tbg3_referer').setValue(document.location.href);
					}
				</script>
				<?php if ($article instanceof TBGWikiArticle): ?>
					<?php include_component('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
				<?php endif; ?>
				<?php /*<h1><?php echo __('Welcome to'); ?> <?php echo(TBGSettings::getTBGname()); ?></h1>
				<?php echo __('Please fill in your username and password below, and press "Continue" to log in.'); ?>
				<br>
				<?php if (TBGSettings::get('allowreg') == true): ?> 
					<?php echo __('If you have not already registered, please use the "Register new account" tab.'); ?>
				<?php else: ?>
					<?php echo __('It is not possible to register new accounts. To register a new account, please contact the administrator.'); ?>
				<?php endif; ?>
				<br><br> */ ?>	
				<div class="logindiv">			
					<div class="rounded_box iceblue">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" method="post" id="login_form" onsubmit="loginUser('<?php echo make_url('login'); ?>'); return false;">
								<input type="hidden" id="tbg3_referer" name="tbg3_referer" value="" />
								<div class="login_boxheader"><?php echo __('Log in to an existing account'); ?></div>
								<div>
									<table border="0" class="login_fieldtable">
										<tr>
											<td><label class="login_fieldheader" for="tbg3_username"><?php echo __('Username'); ?></label></td>
											<td><input type="text" id="tbg3_username" name="tbg3_username" style="width: 200px;"></td>
										</tr>
										<tr>
											<td><label class="login_fieldheader" for="tbg3_password"><?php echo __('Password'); ?></label></td>
											<td><input type="password" id="tbg3_password" name="tbg3_password" style="width: 200px;"></td>
										</tr>
									</table>
									<br>
									<input type="submit" id="login_button" value="<?php echo __('Continue'); ?>">
									<span id="login_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
								</div>
							</form>								
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
				</div>				
			</div>
			<?php TBGEvent::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
			<?php if (TBGSettings::get('allowreg') == true): ?>
				<?php include_template('main/loginregister', array('selected_tab' => $selected_tab)); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="backdrop_detail_content" id="backdrop_detail_indicator" style="text-align: center; padding: 50px; display: none;">
		<?php echo image_tag('spinning_32.gif'); ?>
	</div>
	<div class="backdrop_detail_footer">
	<?php if ($mandatory != true): ?>
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	<?php endif; ?>
	</div>
</div>
<?php if (isset($options['error'])): ?>
	<script type="text/javascript">
		failedMessage('<?php echo $options['error']; ?>');
	</script>
<?php endif; ?>
<script type="text/javascript">
	$('tbg3_username').focus();
</script>