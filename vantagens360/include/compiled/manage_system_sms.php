<?php include template("manage_header");?>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="partner">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_system('sms'); ?></ul>
	</div>
	<div id="content" class="clear mainwide">
        <div class="clear box">
            <div class="box-top"></div>
            <div class="box-content">
                <div class="head"><h2>SMS ()</h2></div>
                <div class="sect">
                    <form method="post">
						<div class="wholetip clear"><h3>1. Basico</h3></div>
                        <div class="field">
                            <label>SMS Usuario</label>
                            <input type="text" size="30" name="sms[user]" class="f-input" value="<?php echo $INI['sms']['user']; ?>"/>
                        </div>
                        <div class="field">
                            <label>SMS senha</label>
                            <input type="text" size="30" name="sms[pass]" class="f-input" value="<?php echo $INI['sms']['pass']; ?>"/>
                        </div>

                        <div class="act">
                            <input type="submit" value="Salvar" name="commit" class="formbutton"/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-bottom"></div>
        </div>
	</div>

<div id="sidebar">
</div>

</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->

<?php include template("manage_footer");?>
