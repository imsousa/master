<?php ?>

<table class="campus">
<caption>Conta Campus</caption>
	 <tr>
    	<td style="">
      <br />
	   <?php if($nao_existe) { ?>
       <span style="color:red">Não encontrada</span> 
       <?php }else { ?>
	   <span style="color:#0495AD"><?php echo $mail; ?></span>
       <?php } ?>
        </td>
        <td style="padding-top:20px;">
           <?php if(!$nao_existe) { ?>
	   		<B>Password expira a</B>: <?php echo $password_expira; ?>
      		 <?php } ?>
        </td>
    </tr>
    <tr>
    	<td colspan="2" style="font-style:italic;">
         <?php if(!$nao_existe) { ?>
       <a style="text-decoration:underline;" href="javascript:dialogas('/servicos/perfil/Campus/help', 'Conta Campus', 600,620)">Para que serve a conta Campus@UL?</a><br />
       <?php }else{ ?>
       <a style="text-decoration:underline;" href="javascript:dialogas('/servicos/perfil/Campus/comoObter', 'Conta Campus', 600,620)">Como obter uma conta Campus@UL?</a><br />
       <?php }?>
      
        </td>
    </tr>
</table>