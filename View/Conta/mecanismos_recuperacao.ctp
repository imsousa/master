<h1>Mecanismos de Recuperação Palavra Passe </h1>
<!--
<span style="font-size:12px;">(<a href="javascript:dialogas('/servicos/perfil/Account/infoMecanismosRecuperacao','Ajuda - Perfil de utilizador',800,400);">Para que serve?</a>)</span>
-->
<table class="nobordertr" >
     <tr>
        <th style="width:180px;"></th>
        <th style=""></th>
         <th style="width:150px;"></th>
     </tr>
     <tr style="height:50px;">
    	<td style=""><strong>Email alternativo</strong></td>
        <td style="" id="input_container_mail">
        <input type="text" name="mail" id="mail"  style="width:300px;" value="<?php echo $utilizador['mecanismo_recuperacao_email']; ?>">
          </td>
        <td>
       <button class="btnCI" onclick="changeAttribute('mail')"><i class="fa fa-save"></i> Guardar</button>
        </td>
    </tr>
    
     <tr style="height:50px;">
    	<td style=""><strong>PIN</strong></td>
        <td >
        <?php echo $utilizador['mecanismo_recuperacao_pin']; ?>
        </td>
        <td>
        
       <button class="btnCI" onclick="gerarPIN()"><i class="fa fa-asterisk"></i> Gerar PIN </button>
        </td>
    </tr>
    
    <tr style="height:50px;">
    	<td style=""><strong>Pergunta</strong></td>
        <td >
            <input type="text" name="pergunta_resposta" id="pergunta_resposta"  style="width:300px;" value="<?php echo $utilizador['mecanismo_recuperacao_pergunta']; ?>">
        
        
      
        </td>
        <td>
          <button class="btnCI" onclick="changeAttribute('pergunta_resposta')"><i class="fa fa-save"></i> Guardar </button>
      
        </td>
    </tr>
    
     <tr style="height:50px;">
    	<td style=""><strong>Resposta</strong></td>
        <td >
     
        
         <input type="password" name="pergunta_resposta_2" id="pergunta_resposta_2"  style="width:300px;" value="******" onfocus="if(this.value == '******') { this.value = ''; }">
         <br />
        
        </td>
        <td>
        
       <button class="btnCI" onclick="changeAttribute('pergunta_resposta_2')"><i class="fa fa-save"></i> Guardar </button>
        </td>
    </tr>
    
</table>
<br />
<!-- <span style="font-size:11px;">
         	* Não é possível alterar a informação colocada na "Pergunta".
         </span>
-->
<script type='text/javascript'>

 
function changeAttribute(attribute) {
	var type = attribute;
	var val = $("#" + attribute).val();
	$.post("/servicos/perfil/Conta/edit", {"type" : type, "val" : val}, function(data) {
		var msg = data;
		if(data == 1)  
			msg = 'Perfil atualizado com sucesso.';		
		infoMsg(msg);
	});
}


function gerarPIN(){
	if(confirm('Tem a certeza que deseja gerar um novo PIN? ')){
		$.post('/servicos/perfil/Conta/edit',{"type": "pin", "val": "random"}, function(data){
		
			if(data==-1){
				infoMsg('Não foi possível gerar um novo PIN.');
			}else{
				infoMsg("Novo pin gerado: " + data);
			}
		});
	}
}

</script>
    






	