<h1>Mudar Palavra Passe</h1>
<span style="font-size:13px;">
<p align="justify">	
            Na sequência da aprovação da Política de Utilização Aceitável das Tecnologias de Informação e Comunicação da FCUL e Regulamentos associados (consulte <a  target="_blank" href="https://www.fc.ul.pt/pt/ui/normas">aqui</a>), a partir de meados de Fevereiro de 2014 a FCUL utiliza uma política mais restritiva para
avaliar a qualidade da palavra passe.<br /><br />
Além das <em>passwords</em> serem obrigatoriamente alteradas anualmente (a palavra passe expira passados 12 meses da sua alteração), foram implementados mecanismos que obrigam a que as palavras passe tenham requisitos de qualidade mínimos.<br /><br />
Esta obrigação será imposta tecnicamente aquando da alteração da palavra passe, mas aconselham-se os utilizadores a adotarem desde já as regras descritas no "Regulamento de Utilização de Contas de Utilizadores da FCUL".<br />
<ul>
    <li>Não contenham três ou mais caracteres consecutivos iguais ao nome de utilizador ou nome completo;</li>
    <li>Contenha pelo menos 6 caracteres;</li>
    <li>Deverá cumprir pelo menos três das seguinte quatro regras:
    <ul>
        <li>Caracteres com letras maiúsculas (A..Z);</li>
        <li>Caracteres com letras minúsculas (a..z);</li>
        <li>Um algarismo (0..9);</li>
        <li>Caracteres não alfabéticos (!,$,#,&,…);</li>
    </ul> 
</li>
</ul>
Também não é possível reutilizar qualquer uma das três palavras passe anteriores.
<br><br><br>

</span>

<table class="nobordertr" >
 <tbody><tr style="">
    	<td style="width:200px;text-align:left;border-bottom:0px solid;"><strong>Palavra passe actual</strong></td>
        <td id="input_container_mail" style="width:260px;text-align:left;border-bottom:0px solid;"><input type="password" id="oldpassword"  style="width:250px;"/></td>
        <td id="" style="width:200px;border-bottom:0px solid;font-size:9px;text-align:left;">Indique a sua palavra passe actual.</td>
      
    </tr>
   <tr>
    	<td style="text-align:left;border-bottom:0px solid;"><strong>Nova palavra passe</strong></td>
        <td id="input_container_pin" style="text-align:left;border-bottom:0px solid;"><input style="width:250px;" type="password" id="newpassword" /></td>
        <td id="" style="border-bottom:0px solid;font-size:9px;text-align:left;">Indique a nova palavra passe.</td>
      
    </tr>
    <tr>
    	<td id="label_pergunta_resposta" style="text-align:left;border:0px solid;"><strong>Confirmar palavra passe</strong></td>
        <td id="input_container_pergunta_resposta" style="text-align:left;border:0px solid;"><input style="width:250px;" type="password" id="confirmpassword" /></td>
        <td style="border:0px solid;font-size:9px;text-align:left;">Indique a confirmação da nova palavra passe.</td>
       
    </tr>
    <tr>
    	<td colspan="3">
        <span style="float:right;">
        <br>
        <button class="btnCI" id="newPassButton" onclick="newPass()"><i class="fa fa-save"></i> Alterar Palavra Passe</button> 
        </span>
        </td>
    </tr>
   
</tbody></table>
<br>

<script type="text/javascript">
function idleClean() {
	$('#oldpassword').val("");
	$('#newpassword').val("");
	$('#confirmpassword').val("");
	infoMsg("Palavra-passe alterada com sucesso.");
	return false;
}	

function newPass() {
	if (empty(document.getElementById('oldpassword'))) {
		infoMsg('<span style="color:red;">A palavra-passe antiga não poder estar vazia</span>'); return false;
		return false;
	}
	if (empty(document.getElementById('newpassword'))) {
		infoMsg("<span style='color:red;'>A palavra-passe nova não deve ser vazia</span>"); return false;
	}
	if (empty(document.getElementById('confirmpassword'))){
		infoMsg('<span style="color:red;">A confirmação da palavra-passe não deve ser vazia</span>');	return false;
	}
	
	if (document.getElementById('newpassword').value != document.getElementById('confirmpassword').value) {
		infoMsg("<span style='color:red;'>A confirmação da palavra-passe não é igual</span>");
		return false;
	}
	/*Chegou até aqui? Pode submeter*/
	//alert("agora mudava.. mm tranqu");
	$.post("/servicos/perfil/Conta/edit/", {"type": "password", "val": $("#oldpassword").val(), "new_pass": $("#newpassword").val()}, function(data){
		if(data == 1){
			$('#newPassButton').hide();
			setTimeout(idleClean, 3000); // avoid resubmissions
			$('#newPassButton').show();
		}
		else
			infoMsg("<span style='color:red;'>" + data + "</span>");
	});
}

function empty(elem){
	if (elem.value == null || elem.value == undefined || elem.value == '') {
		
		return true;
	}
	if (elem.value.length == 0) {
		
		return true;
	}
	return false;
} 

</script>



