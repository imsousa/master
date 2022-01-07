<style type="text/css">
.nofic2015 {
/*color:white;
font-size:10px;
padding-top:2px;
padding-bottom:2px;
*/
display:inline-block;
padding-left:5px;
padding-right:5px;
}

img {
	display:inline-block;
	vertical-align:middle;
}

.tem_notif {
	background-color:rgba(44,63,177,1);
}
</style>
<script type="text/javascript">
function showSenhas() {
	dialogas('/servicos/perfil/SenhasAtendimento/index','Estado online do atendimento presencial (C5)',580,450);
}
<?php
$total_mensagens_por_ler = ($exchange_nr_msgs_por_ler + $moodle_nr_msgs_por_ler);
$class = 'tem_notif';
if($total_mensagens_por_ler>0) { 
	$class="tem_notif";
}
	
	// $msg_mail = '<a target="_blank"  style="color:white;cursor:pointer;text-decoration:none;" title="Ver emails no Webmail" href="http://webmail.ciencias.ulisboa.pt/"><i class="fa fa-envelope"></i> '. $exchange_nr_msgs_por_ler.'</a>';
	$msg_mail = '<a target="_blank"  style="color:white;cursor:pointer;text-decoration:none;" title="Ver emails no Webmail" href="http://webmail.ciencias.ulisboa.pt/"><img src="/servicos/perfil/img/outlook_white.png" width="20px" height="20px"></a>';

	// $msg_moodle = (($exchange_nr_msgs_por_ler>0) ? '&nbsp;' : '&nbsp;').'<a style="color:white;cursor:pointer;text-decoration:none;" target="_blank" title="Ver mensagens no Moodle" href="http://moodle.ciencias.ulisboa.pt/message/?viewing=recentconversations"><i class="fa fa-comments"></i> '.$moodle_nr_msgs_por_ler.'</a>';
	$msg_moodle = '&nbsp;&nbsp;&nbsp;<a style="color:white;cursor:pointer;text-decoration:none;" target="_blank" title="Ver mensagens no Moodle" href="http://moodle.ciencias.ulisboa.pt/message/?viewing=recentconversations"><img src="/servicos/perfil/img/moodle_white.png" width="20px" height="20px"></a>';
 
?>
$(document).ready(function() {
	//mini notif
	if($("#small_notif").length == 0) {
		
	}else{
		$("#small_notif").html('&nbsp;&nbsp;<span style=""  class=" ui-corner-all ciencias <?php echo $class; ?> nofic2015"><?php echo $msg_mail; ?><?php echo $msg_moodle; ?> </span>');
		 
	}
});
</script>

<!--<i class="fa fa-ticket"></i> <a target="_blank" href="javascript:showSenhas()">Secretaria</a>-->