<?php include template("header");?>
<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="maillist">
	<div id="content">
        <div class="box">
            <div class="box-top"></div>
            <div class="box-content welcome">
				<div class="head">
					 <h2>Receba nossas ofertas por email</h2>
				</div>
                <div class="sect">
				  <div class="succ">Parab&eacute;ns! Seu email <strong><?php echo $_POST['email']; ?></strong>&nbsp;receber&aacute; ofertas de <strong><?php echo $city['name']; ?></strong> todos os dias. </div>
				 </div>
			</div>
		</div>
		<div class="box-bottom"></div>
	</div>
	<div id="sidebar">
		<div class="side-pic">
		   <p><img src="/static/img/subscribe-pic1.jpg" /></p>
		   <p><img src="/static/img/subscribe-pic2.jpg" /></p>
		   <p><img src="/static/img/subscribe-pic3.jpg" /></p>
		</div>
	</div>
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->

<?php include template("footer");?>

