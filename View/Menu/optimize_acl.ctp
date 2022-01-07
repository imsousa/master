<?php echo $this->Form->create("optimize"); ?>

<table id="tabela_acl">
	<thead>
    	<tr>
        	<th>Entidade(s) (separadas por virgula)</th>
            <th>Grupo</th>
        </tr>
    </thead>
    <tbody>
    	<tr>
        	<td><?php echo $this->Form->input("entity."); ?></td>
            <td><?php echo $this->Form->input("is_group.", array("type"=>"checkbox",'hiddenField' => false)); ?></td>
        </tr>
        <tr>
        	<td><?php echo $this->Form->input("entity."); ?></td>
            <td><?php echo $this->Form->input("is_group.", array("type"=>"checkbox",'hiddenField' => false)); ?></td>
        </tr>
        <tr>
        	<td><?php echo $this->Form->input("entity."); ?></td>
            <td><?php echo $this->Form->input("is_group.", array("type"=>"checkbox",'hiddenField' => false)); ?></td>
        </tr>
        <tr>
        	<td><?php echo $this->Form->input("entity."); ?></td>
            <td><?php echo $this->Form->input("is_group.", array("type"=>"checkbox",'hiddenField' => false)); ?></td>
        </tr>
        <tr>
        	<td><?php echo $this->Form->input("entity."); ?></td>
            <td><?php echo $this->Form->input("is_group.", array("type"=>"checkbox",'hiddenField' => false)); ?></td>
        </tr>
        <tr>
        	<td>
            	<a href="javascript:addLine()">Adicionar linha</a>
            </td>
            <td>    
            	<?php echo $this->Form->end("Submeter"); ?>
            </td>
        </tr>
    </tbody>
</table>

<div id="optimize_output"></div>

<script>
	function addLine() {
		$("#tabela_acl").find("tr:last").before($("#tabela_acl").find("tr").eq(2).clone());
	}
	
	$("#optimizeOptimizeAclForm").submit(function (ev) {
		ev.preventDefault();
		var href = $(this).attr("action");
		var dados = $(this).serializeArray();
		
		$("#optimize_output").load(href, dados);
	});
</script>
