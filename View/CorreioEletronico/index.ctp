<h1>Correio Eletr&oacute;nico</h1>
<table class="nobordertr" >  
   <tr style="height:60px;">
    	<td style="width:200px;text-align:left;"><strong>Email principal</strong></td>
        <td style="width:210px;text-align:left;"> <input style="width:300px;" type="text" value="<?php echo $utilizador['email']; ?>"></td>
   </tr>
   <!--
    // 0 pendente novo
    // 1 criado
    // 2 pendente pra apagar
    // 4 apagado
	-->
  <tr style="height:80px;">
    	<td style="text-align:left;"><strong>Email reencaminhamento</strong></td>
        <td style="text-align:left;" id="input_container_fwd" >
        <input type="text" name="fwd" id="fwd" style="width:300px;" value="<?php echo $utilizador['email_fwd']; ?>"
        <?php echo (in_array($status_pedido_fwd, array(0,1,2))) ? 'disabled' : ''; ?>
        >
        <?php 
			if(empty($utilizador['email_fwd']) && $role == 0) {      ?>
            <br>
            <input type="checkbox" name="fwd_new_mail_localcopy" id="fwd_new_mail_localcopy" > Manter cópia local
		<?php } ?>
     
        </td>
        <td style="text-align:right;padding-right:50px;">
        <?php
		switch($status_pedido_fwd){
			case -1: echo '&nbsp;<button class="btnCI" onclick="adicionarFWD()">
							<i class="fa fa-save" ></i> Guardar
						   </button>';
						   break;
			case 0: echo '(Em actualização)'; break;
			case 1: echo '<button class="btnCI confirm" onclick="removerFWD()">
						  <i class="fa fa-remove"></i> Remover
						  </button>'; 
						  break;
			case 2: echo '(A remover reencaminhamento)';
		}
  		?></td>
    </tr>
    <?php if($acesso_ao_aquivo) { ?>
    <tr style="height:60px;">
    	<td><strong>Arquivo</strong></td>
        <?php if($arquivo_ativo) { ?>
        <td colspan="2"><span style="color:green;">Ativado</span> (<a href="https://ciencias.ulisboa.pt/pt/node/6220/" target="_blank">Saber mais</a>)</td>
        <?php }else{ ?>
        <td style="line-height:13px;">
       	 <input type="checkbox" name="aceito_arquivo_sem_volta" id="aceito_arquivo_sem_volta" style="float:left;" />
         Após activar o arquivo de e-mail, não é possível reverter, mais informações sobre o arquivo <a href="https://ciencias.ulisboa.pt/pt/node/6220/" target="_blank">aqui</a>.
        </td>
        <td style="text-align:right;padding-right:50px;">
        
           <button class="btnCI" onclick="adicionarArquivo()"><i class="fa fa-check"></i> Ativar</button>
        </td>
        <?php } ?>
    </tr>
    <?php } ?>
  </table>
  
  <br />
  <br />
  
  <h4>Alias(es)</h4>
  
         	<table class="nobordertr">
				<?php $count_aprovados_e_pendentes = 0; ?>
                <?php foreach($utilizador['aliases'] as $alias) { ?>
                <tr>
                	<td><strong>Alias(es) Ativos:</strong></td>
                	<td style="min-width:296px;">
                    <?php echo $alias; ?>
                    </td>
                    <td>
                    <button class="btnCI" onclick="removerAlias('<?php echo $alias; ?>')"><i class="fa fa-remove"></i> Remover</button> 
                    </td>
                    <?php $count_aprovados_e_pendentes++; ?>
                </tr>
                <?php } ?>
                 <?php foreach($aliases_pendentes as $alias) { ?>
                 <tr>
                 	<td><strong>Pendentes</strong>:</td>
                	<td>
					<?php echo $alias['alias']; ?>
					</td>
					<td>
					<?php echo ($alias['estado']==1) ? '(Pendente)' : ''; ?>
                    </td>
                  </tr>
                      <?php $count_aprovados_e_pendentes++; ?>
                <?php } ?>

                <?php if($count_aprovados_e_pendentes<2) { ?>
                    <tr>
                    	<td><strong>Adicionar novo:</strong></td>
                        <td>
                        <input type="text" name="new_alias" id="new_alias" />@<?php echo $utilizador['dominio'];?>
                        </td>
                        <td>
                        <button class="btnCI" onclick="adicionarAlias()"><i class="fa fa-save"></i> Guardar</button>
                        </td>
                    </tr>
                <?php } ?>
                
            </table>

  
<script type="text/javascript">
  
  
  function adicionarArquivo() {
	  if(confirm('Tem a certeza que deseja adicionar o arquivo de email?')) {
		var checked = 0;
		if ($("#aceito_arquivo_sem_volta").is(":checked")) {
		   checked = 1;
		}else{
			infoMsg("Tem de clicar na checkbox para confirmar a ativação de arquivo no e-mail.");
		}
		if(checked==1) {
			$.post("/servicos/perfil/CorreioEletronico/ativarArquivoEmail", {"checked" : checked}, function(data) {
					var msg = data;
					if(data == 1)   {
						msg = 'Arquivo ativado com sucesso.';		
						reloadPage('/servicos/perfil/CorreioEletronico');
					}
					else
						msg = '<span style="color:red;">' + data + '</span>';
					infoMsg(msg);
					
				});
		  }
	  }
	}
  
  function adicionarFWD() {
	  if(confirm('Tem a certeza que deseja adicionar o redireccionamento?')) {
		var checked = 0;
		<?php if($role == 0) { ?>
		if ($("#fwd_new_mail_localcopy").is(":checked")) {
		   checked = 1;
		}
		<?php } ?> 
		$.post("/servicos/perfil/CorreioEletronico/edit", {"type" : 'fwd', "mail" : $("#fwd").val(), "checkbox": checked}, function(data) {
				var msg = data;
				if(data == 1)   {
					msg = 'Perfil atualizado com sucesso.';		
					reloadPage('/servicos/perfil/CorreioEletronico');
				}
				else
					msg = '<span style="color:red;">' + data + '</span>';
				
				infoMsg(msg);
				
			});
	  }
	}
	
	
	function adicionarAlias() {
	  if(confirm('Tem a certeza que deseja adicionar o alias ?')) {
		$.post("/servicos/perfil/CorreioEletronico/edit", {"type" : 'novoAlias', "alias" : $("#new_alias").val()}, function(data) {
				var msg = data;
				if(data == 1)   {
					msg = 'Perfil atualizado com sucesso.';		
					reloadPage('/servicos/perfil/CorreioEletronico');
				}
				else
					msg = '<span style="color:red;">' + data + '</span>';
				infoMsg(msg);
				
			});
	  }
	}
	
	
	function removerAlias(alias) {
		if(confirm('Tem a certeza que deseja remover o alias?')) {
			$.post("/servicos/perfil/CorreioEletronico/edit", {"type" : 'removerAlias', "alias" : alias}, function(data) {
			var msg = data;
			if(data == 1)   {
				msg = 'Perfil atualizado com sucesso.';		
				reloadPage('/servicos/perfil/CorreioEletronico');
			}
			else
				msg = '<span style="color:red;">' + data + '</span>';
			infoMsg(msg);
			}); 
		}
	 }
	
   function removerFWD() {
		if(confirm('Tem a certeza que deseja remover o redireccionamento?')) {
			$.post("/servicos/perfil/CorreioEletronico/edit", {"type" : 'remove_fwd', "mail" : $("#fwd").val()}, function(data) {
			var msg = data;
			if(data == 1)   {
				msg = 'Perfil atualizado com sucesso.';		
				reloadPage('/servicos/perfil/CorreioEletronico');
			}
			else
				msg = '<span style="color:red;">' + data + '</span>';
			infoMsg(msg);
			}); 
		}
	 }
</script>