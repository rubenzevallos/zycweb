<?php include template("header");?>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="learn">
	<div class="dashboard" id="dashboard">
		<ul><?php echo current_help('tour'); ?></ul>
	</div>
	<div id="content" class="about clear">
        <div class="box">
            <div class="box-top"></div>
            <div class="box-content">
                <div class="head"><h2>Como funciona isso</h2></div>
                <div class="sect guide">
					<ul class="guide-steps">
                        <li>
                            <h3 class="step1">Veja a oferta do dial</h3>
                            <p class="text">Sempre tem otimas ofertas, em diferentes segmentos!</p>
                        </li>
                        <li>
                            <h3 class="step2">Compre</h3>
                            <p class="text">
                                Se você gostar da ofertar, clique em comprar.
                                <img src="/static/img/learn-guide-buy.jpg" style="margin-left:-9px;" />
                            </p>
                            <div class="bubble">
                                <div class="bubble-top">
                                    <ol class="buy">
                                        <li>Cada oferta fica disponivel em um dia.</li>
                                        <li>Chame seu amigos e ganhe!</li>
                                        <li class="last">Se a oferta não atingir o numero minimo de compradores, devolvemos seu dinheiro.</li>
                                    </ol>
                                </div>
                                <div class="bubble-bottom"></div>
                            </div>
                        </li>
                        <li>
                            <h3 class="step3">Compre <?php echo $INI['system']['couponname']; ?></h3>
                            <p class="text">Se a oferta atingir o minimo, enviamos seu cupom.</p>
                            <div class="bubble">
                                <div class="bubble-top">
                                    Você pode receber de duas maneiras:
                                      <ol class="coupon">
                                        <li><strong>Email</strong><br>
                                            <p>Você recebe por email.</p>
                                        </li>
                                        <li><strong>Imprimir no site</strong>
                                            <p>Você pode imprimir direamente pelo site, no link sua conta.</p>
                                        </li>
                                    </ol>
                                </div>
                                <div class="bubble-bottom"></div>
                            </div>
                        </li>
                        <li>
                            <h3 class="step4">Vá ao local</h3>
                            <p class="text">
                                Apresente seu cupom e desfrute do disconto!
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-bottom"></div>
        </div>
	</div>
	<div id="sidebar">
		<?php include template("block_side_business");?>
		<?php include template("block_side_subscribe");?>
	</div>
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->

<?php include template("footer");?>

