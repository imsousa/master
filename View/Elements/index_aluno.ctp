<?php 


if(strlen(trim($utilizador['mecanismo_recuperacao_pergunta']))==0 || strlen(trim($utilizador['mecanismo_recuperacao_resposta']))==0 || strlen(trim($utilizador['mecanismo_recuperacao_email']))==0) { 
?>
	<div align="justify" class="alert alert-danger">
	   <!--Olá <strong><?php echo ($utilizador['nome_completo']); ?></strong>,<br />
       <br />
       
       De acordo com as normas definidas no <a href="https://ciencias.ulisboa.pt/sites/default/files/fcul/unidservico/ui/normas/Regulamento-Utilizadores_v1.0.pdf">Regulamento de Utilizadores</a> apenas é possível alterar a sua palavra passe via correio electrónico uma única vez. Por esse motivo o seu perfil encontra-se bloqueado até definir os mecanismos de recuperação de conta: <em>email alternativo</em>, <em>pergunta</em> e <em>resposta</em>.<br />
      
       <br />
       -->
       
       Para continuar a utilizar o Portal de Ciências, por favor defina os seguintes mecanismos de recuperação de conta: email alternativo, pergunta e resposta. Deverá guardar todos os campos individualmente.
		 <br />
        </div>
<br /><br />
    <div id="mec"></div>
    <br />
    <br />
    	<div align="justify" class="alert alert-info">
	   
       <a style="text-decoration:underline;cursor:pointer;" href="/user"><strong>Prosseguir para o Portal após definição dos mecanismos </strong></a> *
       
      
       <br />
       <span style="font-size:11px;">* <em>Os dados podem demorar 20 segundos até estarem completamente atualizados.</em></span>
    
       
       <br />

        </div>
    <script type="text/javascript">
        $(document).ready(function() {
        //$("nav").hide('fast');
            $.get("/servicos/perfil/Conta/mecanismosRecuperacao", function(data){
                $("#mec").html(data);
            });
        });
     </script>
     <style type="text/css">
        ul.nav{
            display:none;
        }
     </style>
<?php 
}else{
?>

<h1>A minha conta</h1>

<style type="text/css">
.content tr {
    border-bottom:0px solid black;
}
</style>

<div id="dados_aluno"></div>

<table style="margin-bottom:40px;" class="limpo"> 
    <caption>Detalhes da conta</caption>
   <tr>
   		<th style="width:200px;"></th>
        <th style="min-width:230px;"></th>
        
   </tr>
   <tr>
   		<td><strong>Nome completo</strong></td>
        <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['nome_completo']); ?>" /></td>
        
   </tr>
 
   <tr>
        <td><strong>Username</strong></td>
        <td><input type="text" class="input_show"  readonly="readonly" style="width:100%;" value="<?php echo $utilizador['username']; ?>" /></td>
         
    </tr>
    <tr>
         <td><strong>Email</strong></td>
         <td><input type="text" class="input_show"  readonly="readonly" style="width:100%;" value="<?php echo $utilizador['mail']; ?>" /></td>
        
    </tr>
    <tr>
         <td><strong>BI</strong></td>
         <td><input type="text"  class="input_show" readonly="readonly" style="width:100%;" value="<?php echo $utilizador['bi']; ?>" /></td>
        
    </tr>
   
      <tr>
       	  <td><strong>Conta criada em</strong></td>
          <td><input type="text"  class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['data_criacao_conta']); ?>" /></td>
         
      </tr>
      <tr>
          <td><strong>Palavra passe expira a</strong></td>
          <td><input type="text" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['data_expiracao_password']); ?>" /></td>
         
       </tr>
       <tr>
          <td><strong>Área de Ficheiros</strong></td>
          <td>
          
          
          <input type="text"  class="input_show"  readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['armazenamento']!=NULL) ? $utilizador['armazenamento'] : ''; ?>" /> 
          
         </td>
       </tr>
        <?php if($grupos!=NULL )  { ?>
        <tr>
           <td style="vertical-align:top;"><strong>Grupos</strong></td>
          <td>
          <table>
        		<?php foreach($grupos as $g) {  ?>
						<tr>
							<td><strong><?php echo $g['nome']; ?></strong></td>
							<td><?php echo $g['descricao']; ?></td>
						</tr>					
				<?php } ?>
				</table>
	
		
         </td>
       </tr>
       <?php } ?>
       </table>
   

<!--<div id="campus"></div>
<br />
<br />-->

<div  id="moodle_discips"></div>


<script type="text/javascript">
$(document).ready(function(){
	
	//$("#moodle_discips").html('<img src="/servicos/common/img/ajax-loader.gif" />');
	//$("#dados_aluno").html('<img src="/servicos/common/img/ajax-loader.gif" />');
	//$("#campus").html('<img src="/servicos/common/img/ajax-loader.gif" />');
	
	$.get("/servicos/alunos/Moodle/", function(data){
		$("#moodle_discips").html(data);
	});
	/*$.get("/servicos/alunos/Ficha/", function(data){
		$("#dados_aluno").html(data);
	});*/
	/*$.get("/servicos/perfil/Campus/", {"cd_aluno" : '<?php echo $utilizador['cd_aluno']; ?>'}, function(data){
		$("#campus").html(data);
	});*/
	
	
});
</script>
<?php } ?>
