<?php include template("manage_html_header");?>

<script type="text/javascript" src="/static/js/xheditor/xheditor.js"></script>

<div id="hdw">

	<div id="hd">

		<div id="logo"><a href="/index.php" class="link" target="_blank"><img src="/static/css/i/logo.png" /></a></div>

		<div class="guides">

			<div class="city">

				<h2>Gerência</h2>

			</div>

			<div id="guides-city-change" class="change"><?php echo $login_user['realname']; ?></div>

		</div>

		<ul class="nav cf"><?php echo current_backend('super'); ?></ul>

	</div>

</div>



<?php if($session_notice=Session::Get('notice',true)){?>

<div class="sysmsgw" id="sysmsg-success"><div class="sysmsg"><p><?php echo $session_notice; ?></p><span class="close">Fechar</span></div></div>

<?php }?>

<?php if($session_notice=Session::Get('error',true)){?>

<div class="sysmsgw" id="sysmsg-error"><div class="sysmsg"><p><?php echo $session_notice; ?></p><span class="close">Fechar</span></div></div>

<?php }?>



