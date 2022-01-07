<?php /*pr($senhas); die();*/?>
<table style="width:100%" class="ciencias  numeros  ">

	<thead>
		<tr>
			<th>Tipo</th>
			<th style="min-width:200px;">Descrição</th>
			<th>Senha Atual</th>
			<th>Senhas em Espera</th>
            <th>Tempo (estimado) de espera</th>
        </tr>
	</thead>
    <tbody>
    	<?php for($i=0; $i < count($senhas); $i++) { ?>
        <tr>
        	<td  style="text-align:center"><?php echo $senhas[$i]->cod_servico; ?></td>
            <td><b><?php echo $senhas[$i]->descr_servico; ?></b></td>
            <?php if($senhas[$i]->aberto==1) { ?>
            <td style="text-align:center"><?php echo $senhas[$i]->senha_atual; ?></td>
            <td style="text-align:center"><?php echo $senhas[$i]->pessoas_em_espera; ?></td>
            <td  style="text-align:center"><?php echo $senhas[$i]->tempo_previsto; ?> minutos</td>
            <?php }else{ ?>
             <td style="text-align:center; color:red;"> - </td>
            <td style="text-align:center; color:red;"> - </td>
            <td  style="text-align:center; color:red;">Encerrado</td>
            <?php }?>
            
            
        </tr>
        <?php } ?>
    </tbody>
</table>
Atualizado às <?php echo date('Y-m-d H:i:s'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".ui-dialog-buttonset").html ('<button class="btnCI btnCI-mini" onclick="closeDiag()">Fechar</button>');
});

function closeDiag() 
{
	jQuery(dialog).remove();
}
</script>