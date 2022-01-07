<?php ?>
<div id="res_senhas">

</div>
<script type="text/javascript">
var iFrequency = 5000; // expressed in miliseconds
var myInterval = 0;
jQuery(document).ready(function(){
	startLoop();
});

function switchStep() {
	// $("#res_senhas").html('<img src="http://static.fc.ul.pt/www/img/ajax_gifs/loader_horizontal.gif">');
	jQuery.post("/servicos/perfil/SenhasAtendimento/data", {"get":"data"}, function(data) {
		jQuery("#res_senhas").html(data);
	});
}

function startLoop() {
	
	switchStep();
	<?php if(!$norefresh) { ?>
	if(myInterval > 0) 
		clearInterval(myInterval);  // stop
	myInterval = setInterval( "switchStep()", 15000 );  // run
	<?php }?>
}
</script>