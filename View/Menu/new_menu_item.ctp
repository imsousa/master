<?php echo $this->Form->create("MenuItem", array('inputDefaults' => array('label' => false, 'div' => false))); ?>

<table style="width:100%" class="dados"> 
	<tr class="even">
    	<td >Label</td>
    	<td colspan=5>
        <?php echo $this->Form->input("MenuItem.label", array("style" => "width:95%")); ?>
        </td>
    </tr>
    <tr>
    	<td>URL</td>
    	<td colspan=5>
        <?php echo $this->Form->input("MenuItem.url", array("style" => "width:95%")); ?>
        </td>
    </tr>
    <tr class="even">
    	<td>Parent</td>
    	<td colspan=5>
        <?php
		$parents = array(0 => "Nenhum");
		foreach ($menu as $existing){
			$parents[$existing["MenuItem"]["id"]] = $existing["MenuItem"]["label"];
		} 
		echo $this->Form->input("MenuItem.parent_id",array("type"=>"select", "options"=>$parents, "style"=>"width:200px;")); 
		?>
        </td>
    </tr>
    <tr>
    	<td  width="100">Full URL
        <?php echo $this->Form->input("MenuItem.full_url",array("type"=>"checkbox")); ?>
        </td>
    	<td  width="100">Abre Janela
        <?php echo $this->Form->input("MenuItem.other_window",array("type"=>"checkbox")); ?>
        </td>
        <td  width="100">Publico
        <?php echo $this->Form->input("MenuItem.public",array("type"=>"checkbox")); ?>
        </td>
         <td  width="100">Plano B
        <?php echo $this->Form->input("MenuItem.plano_b",array("type"=>"checkbox")); ?>
        </td>
    </tr>
    <tr class="even">
    	<td colspan=6 style="text-align:right;">
        <?php echo $this->Form->end(array("label"=>"Gravar", "class"=>"btnCI btnCI-default"));?>
        </td>
    </tr>
</table>	

<div id="newMenuItemOutput"></div>

<script type="text/javascript">
$("#MenuItemNewMenuItemForm").submit(function (ev){
	ev.preventDefault();
	var href = $(this).attr("action");
	var dados = $(this).serializeArray();
	
	$.post(href, dados, function(response) {
		if(response.success){
			$("#refreshMenuButton").click();				
			$("#newMenuItemOutput").html("<div class='alert alert-success'>Menu Item gravado com sucesso<div>");
		} else {
			$("#newMenuItemOutput").html("<div class='alert alert-error'>"+response.msg+"<div>");
		}
	}, 'json');
});
</script>			