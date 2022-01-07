<?php echo $this->Form->create("MenuAcl", array('inputDefaults' => array('label' => false, 'div' => false))); ?>

<table style="width:100%" class="dados">
	<thead>
    	<tr>
        	<th>Entidade</th>
            <th>Grupo</th>   
            <th></th>         
        </tr>
    </thead>
    <tbody >
    	<?php foreach($this->request->data as $i => $entry) { ?>
    	<tr class="menu_item_row <?php echo $even ? "even" : "" ?>">
        	<td>
				<?php echo $this->Form->input("$i.MenuAcl.id",array("type"=>"hidden")); ?>
            	<?php echo $this->Form->input("$i.MenuAcl.menu_item_id",array("type"=>"hidden")); ?>
                <?php echo $this->Form->input("$i.MenuAcl.entity", array("style"=>"width:300px;")); ?>
            </td>
            <td>
            	<?php echo $this->Form->input("$i.MenuAcl.is_group"); ?>
            </td>
            <td>
            	<a class="deleteAclItem" href="/servicos/perfil/Menu/deleteAclEntry/<?php echo $entry["MenuAcl"]["id"];?>">
                	<i class="fa fa-remove"></i>
                </a>
            </td>            
        </tr>
        <?php $even = !$even; } ?>
        <tr id="menu_item_action_row">
        	<td>
            	<button class="btnCI" onclick="addLines()" ><i class="fa fa-plus"></i> Adicionar Linhas:</button> <input id="num_linhas" value="1" size="2"/>
            </td>
        	<td style="text-align:right;" colspan=2> 
            	<?php echo $this->Form->button("<i class='fa fa-check'></i> Gravar", array("class"=>"btnCI", "escape"=>false));?>
            </td>
        </tr>
    </tbody>
</table>


<br />
<?php 
$menuList = array();
foreach($menu as $pItem) {
	$menuList[$pItem["MenuItem"]["id"]] = $pItem["MenuItem"]["ordem"]." - ".$pItem["MenuItem"]["label"];
	foreach($pItem["children"] as $cItem){
		$menuList[$cItem["MenuItem"]["id"]] = $pItem["MenuItem"]["ordem"].".".$cItem["MenuItem"]["ordem"]." - ".$cItem["MenuItem"]["label"];
	}
} 
?>
<table>
	
    <tbody>
    	<tr>
        	<td>Copiar ACL:</td>
            <td><?php echo $this->Form->select('copy_from',$menuList, array("style"=>"width:344px;"));?></td>
            <td>
            	&nbsp;<button class="copiarAclDe btnCI" title="Ao copiar a Acl de outro menu a Acl original vai ser apagada"
                	href="/servicos/perfil/Menu/copyAcl"><i class="fa fa-check"></i> Copiar                </button>
            </td>
        </tr>
    </tbody>
</table>

<script type="text/javascript">
$("#MenuAclEditarAclForm").submit(function (ev) {
	ev.preventDefault();
	var href = $(this).attr("action");
	var dados = $(this).serializeArray();
	
	var container = $("#MenuAclEditarAclForm").parent();
	$(container).html("<img  src='/servicos/common/img/ajax-loader.gif'/>")
	$(container).load("/servicos/perfil/Menu/editarAcl/<?php echo $menu_item_id; ?>", dados);	
});

$(".copiarAclDe").click(function (ev) {
	ev.preventDefault();
	var href = $(this).attr("href");
	var copy_from = $("#MenuAclCopyFrom").val();
	if (copy_from === "")
		return false;
		
	var dados = {
		from : copy_from, 
		to   : "<?php echo $menu_item_id; ?>"
	};
	
	$.post(href, dados, function () {
		var container = $("#MenuAclEditarAclForm").parent();
		$(container).html("<img src='/servicos/common/img/ajax-loader.gif'/>")
		$(container).load("/servicos/perfil/Menu/editarAcl/<?php echo $menu_item_id; ?>");
	});
});

$(".deleteAclItem").click(function (ev) {
	ev.preventDefault();
	
	if(!confirm("tem a certeza que deseja apagar este Acl da base de dados")){
		return false;
	}
	
	var href = $(this).attr("href");
	$.post(href, function () {
		var container = $("#MenuAclEditarAclForm").parent();
		$(container).html("<img  src='/servicos/common/img/ajax-loader.gif'/>")
		$(container).load("/servicos/perfil/Menu/editarAcl/<?php echo $menu_item_id; ?>");		
	});
});

function addLines() {
	var numLinesToAdd = $("#num_linhas").val();
	var numLines = $(".menu_item_row").length;
	
	var even, lastLine;
	for(i = 0; i < numLinesToAdd; i++){
		lastLine = $(".menu_item_row:last");
		
		even = true;
		if ($(lastLine).hasClass("even"))
			even = false;
		
		linha = "<tr class='menu_item_row "+(even ? "even" : "")+"'>";
		linha += "	<td>";
		linha += '		<input id="'+numLines+'MenuAclMenuItemId" type="hidden" value="<?php echo $menu_item_id; ?>" name="data['+numLines+'][MenuAcl][menu_item_id]">';
		linha += '		<input id="'+numLines+'MenuAclEntity" type="text" value="" maxlength="255" style="width:300px;" name="data['+numLines+'][MenuAcl][entity]">';
		linha += "	</td>";
		linha += "	<td>";
		linha += '		<input id="'+numLines+'MenuAclIsGroup_" type="hidden" value="0" name="data['+numLines+'][MenuAcl][is_group]">';
		linha += '		<input id="'+numLines+'MenuAclIsGroup" type="checkbox" value="1" name="data['+numLines+'][MenuAcl][is_group]">';
		linha += "	</td>";
		linha += "	<td><button class='btnCI' onclick='$(this).parent().parent().remove()'><i class='fa fa-remove'></i></button></td>";
		linha += "</tr>";
		
		if (lastLine.length < 1){
			$("#menu_item_action_row").before(linha);
		} else {
			$(lastLine).after(linha);
		}
				
		numLines++;
	}	
}
</script>