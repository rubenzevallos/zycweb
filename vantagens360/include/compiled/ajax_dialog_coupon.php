<div id="order-pay-dialog" class="order-pay-dialog-c" style="width:380px;">
<h3><span id="order-pay-dialog-close" class="close" onclick="return X.boxClose();">Fechar</span>Verificar <?php echo $INI['system']['couponname']; ?></h3>
	<p class="info" id="coupon-dialog-display-id">Por favor insira o codigo do <?php echo $INI['system']['couponname']; ?><br/>Serial e Senha<br/></p>
	<p class="notice"><?php echo $INI['system']['couponname']; ?>Serial: <input id="coupon-dialog-input-id" type="text" name="id" class="f-input" style="text-transform:uppercase;" maxLength="12" onkeyup="X.coupon.dialoginputkeyup(this);" /></p>
	<p class="notice"><?php echo $INI['system']['couponname']; ?>Senha: <input id="coupon-dialog-input-secret" type="text" name="secret" style="text-transform:uppercase;" class="f-input" maxLength="8" onkeyup="X.coupon.dialoginputkeyup(this);" /></p>
	<p class="act"><input id="coupon-dialog-query" class="formbutton" value="é valido?" name="query" type="submit" onclick="return X.coupon.dialogquery();"/>&nbsp;&nbsp;&nbsp;<input id="coupon-dialog-consume" name="consume" class="formbutton" value="Informar consumo(precisa da senha)" type="submit" onclick="return X.coupon.dialogconsume();" ask="Each <?php echo $INI['system']['couponname']; ?> only can be used once, are you sure?"/></p>
</div>

