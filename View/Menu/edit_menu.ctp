<h1>Gestão Menu</h1>
<style type="text/css">
.parent td {
	border-top: 1px solid #000;	
	border-bottom: 0;
	background-color: #ccc;	
}

.child td {
	border: 0;
}

.parent td:last-child {
	border-right: 1px solid #000;
}

.parent td:first-child{
	border-left: 1px solid #000;
}

.child td:last-child {
	border-right: 1px solid #000;
}

.child td:first-child{
	border-left: 1px solid #000;
}

.child td:nth-child(2){
	padding-left:20px;
}

.highlight-active td {
	background-color:#EEE;	
}

.highlight-hover td {
	background-color:#CCC;
}

#menu-table tr:last-child td {
	border-bottom: 1px solid #000;
}
</style>



<button class="btnCI btnCI-default dialog"  id="voltar" title="Criar Menu Item" href="/servicos/perfil/Menu/newMenuItem" >
<i class="fa fa-plus"></i> Novo  Item Menu
</button>


<button class="btnCI btnCI-warning ajax" href="/servicos/perfil/Menu/editMenu"  id="refreshMenuButton"  >

              <i class="fa fa-refresh"></i>
                Refresh Menu
           
</button>


<button class="btnCI btnCI-inverse minimizeAll">
<i class="fa fa-minus"></i> Minimizar Menu
</button>




<button class="btnCI btnCI-info dialog"  id="voltar" title="Ajuda" href="/servicos/perfil/Menu/help" >
<i class="fa  fa-question-circle "></i> Ajuda
</button>
<br /><br />





<?php echo $this->Form->create("MenuItem", array(
				'inputDefaults' => array('label' => false, 'div' => false))); ?>

<?php echo $this->Session->flash(); ?><br/>


                                                 
<table class="dados" >
<caption>Menu

<span style="float:right;">
<?php echo $this->Form->button("<i class=' fa fa-check '></i> Gravar Alterações", array("class"=>"btnCI btnCI-success",
																		 "style" => "float:right;margin-top:10px;margin-bottom:10px;", 
																		 "escape"=>false));?>
</span>
</caption>
	
    	<tr>
        	<th  style="width:5px">Id</th>
        	<th style="width:100px" title="Testo que aparece no menu.">Label</th>
            <th  title="Url do link (/servicos/servico/controler/acçao)" style="width:100%">URL</th>
            <th style="width:30px" title="Ordem no menu, de cima a baixo.">Order</th>
            <th  style="width:30px" title="Caso esteja checked o URL é inteiro, caso contrario é do tipo #page=url">Url</th>
            <th  style="width:30px" title="Abrir numa janela diferente (target _blank).">Jan.</th>
            <th  style="width:30px" title="Link publico, links filhos dependem do Acl do pai.">Pub.</th>
            <th  style="width:40px" title="Servico em modo Plano B."><span style="font-size:10px;">PlanoB</span></th>
            <th  style="width:40px" title="Visivel"><i class="fa fa-eye"></i></th>
            <th>
            	
            </th>
        </tr>
  
    <tbody>
    	<?php foreach($menu as $i => &$menuItem) { ?>
        <tr class="draggable parent" data-parent="0" data-id="<?php echo $menuItem["MenuItem"]["id"]; ?>">
        	<td class="toggleChildren">
            	<?php echo $menuItem["MenuItem"]["id"]; ?>
            </td>
            <td class="toggleChildren">
            	<?php echo $this->Form->input($i.".MenuItem.id",array("type" => "hidden")); ?>
                <?php echo $this->Form->input($i.".MenuItem.parent_id",array("type" => "hidden")); ?>
				<?php echo $this->Form->input($i.".MenuItem.label"); ?>
            </td>
            <td class="toggleChildren">
				<?php echo $this->Form->input($i.".MenuItem.url", array("style"=>"width:100%")); ?>
            </td>
            <td class="toggleChildren">
				<?php echo $this->Form->input($i.".MenuItem.ordem", array("style"=>"width:30px","readonly"=>true)); ?>
            </td>
            <td class="">
				<?php echo $this->Form->input($i.".MenuItem.full_url"); ?>
            </td>
            <td class="">
				<?php echo $this->Form->input($i.".MenuItem.other_window"); ?>
            </td>
            <td class="">
				<?php echo $this->Form->input($i.".MenuItem.public"); ?>
            </td>
            <td class="">
				<?php echo $this->Form->input($i.".MenuItem.plano_b"); ?>
            </td>
            <td class="">
				<?php echo $this->Form->input($i.".MenuItem.visible"); ?>
            </td>
            <td nowrap="nowrap" style="font-size:16px;">
            	<a class="dialog" title="Criar novo menu item filho deste"
                	href="/servicos/perfil/Menu/newMenuItem/<?php echo $menuItem["MenuItem"]["id"]; ?>">
                	<i class="fa fa-plus"></i>
                </a>
            	<a class="row_action" data-action="moveUp" title="Subir na ordem">
               	<i class="fa fa-arrow-circle-o-up"></i>
                </a>
                <a class="row_action" data-action="moveDown" title="Descer na ordem">
               	<i class="fa fa-arrow-circle-o-down"></i>
                </a>
                <a class="dialog" data-action="acl" title="Editar Acessos de <?php echo $menuItem["MenuItem"]["label"]; ?>" 
                	href="/servicos/perfil/Menu/editarAcl/<?php echo $menuItem["MenuItem"]["id"]; ?>">
                	<i class="fa fa-list"></i>
                </a>
                <a onclick="addIcon(<?php echo $menuItem["MenuItem"]["id"]; ?>)">
                	<i class="fa  fa-file-image-o "></i>
                </a>  
                <a class="row_action" data-action="delete" title="Apagar">
                	<i class="fa fa-remove"></i>
                </a>                  
            </td>
        </tr>
			<?php foreach($menuItem["children"] as $j => &$menuItemChild) { ?>
        <tr class="draggable child" data-parent="<?php echo $menuItem["MenuItem"]["id"]; ?>" data-id="<?php echo $menuItemChild["MenuItem"]["id"]; ?>" style="display:none;">
           	<td>
           		<?php echo $menuItemChild["MenuItem"]["id"]; ?>
            </td>
            <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.id",array("type" => "hidden")); ?>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.parent_id",array("type" => "hidden")); ?>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.label"); ?>
            </td>
            <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.url", array("style"=>"width:200px")); ?>
            </td>
            <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.ordem", array("style"=>"width:30px","readonly"=>true)); ?>
            </td>
            <td>
				<?php echo $this->Form->input($i.".children.".$j.".MenuItem.full_url"); ?>
            </td>
            <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.other_window"); ?>
            </td>
            <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.public"); ?>
            </td>
             <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.plano_b"); ?>
            </td>  <td>
                <?php echo $this->Form->input($i.".children.".$j.".MenuItem.visible"); ?>
            </td>
            <td style="font-size:12px;">
            	<a class="row_action" data-action="moveUp" title="Subir na ordem">
                	<i class="fa fa-arrow-circle-o-up"></i>
                </a>
                <a class="row_action" data-action="moveDown" title="Descer na ordem">
                	<i class="fa fa-arrow-circle-o-down"></i>
                </a>
                <a class="dialog" data-action="acl" title="Editar Acessos de <?php echo $menuItemChild["MenuItem"]["label"]; ?>" 
                	href="/servicos/perfil/Menu/editarAcl/<?php echo $menuItemChild["MenuItem"]["id"]; ?>">
                	<i class="fa fa-list"></i>
                </a>
                <a class="row_action" data-action="delete" title="Apagar">
                	<i class="fa fa-remove"></i>
                </a>                                
            </td>
        </tr>
        	<?php } ?>
        <?php } ?>
    </tbody>
</table>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
	$(document).off("click", ".dialogConcursos");
	$(document).on("click", ".dialogConcursos", function (e){
		e.preventDefault();
		e.stopPropagation();
		var href = this["href"];
		var dialog = document.createElement("div");  
		
		var w = '600';
		if ($(this).hasClass("bigDialog"))
			w = "980"
		
		
		$(dialog).html("<img src='/servicos/common/img/ajax-loader.gif'/>");
		
		$(dialog).load(href, function (data) {
			$(dialog).dialog( "option", "position", "center" );	
		});      
		var dialogOpts = {
			title: this["title"],
			modal: true,
			width: w,
			autoSize: true,
			close: function(event, ui)
			{
				$(dialog).dialog("destroy").remove();
			}
		};
		$(dialog).dialog(dialogOpts);   
		$(dialog).dialog('open');
		
	});

	$("#MenuItemEditMenuForm").submit(function (ev) {
		$(this).after("<img src='/servicos/common/img/ajax-loader.gif'/>");
		
		ev.preventDefault();
		var href = $(this).attr("action");
		var dados = $(this).serializeArray();
		
		$(this).parent().load(href,dados);
	});	
	
	$(".toggleChildren").click(function (ev) {
		ev.preventDefault();
		//console.log(ev.originalEvent.target);
		var row = $(this).parents("tr");
		$("[data-parent='"+$(row).data("id")+"']").toggle();		
	});
	
	$(".minimizeAll").click(function (ev) {
		ev.preventDefault();
		$(".child").hide();
	});
	
	$(".row_action").click(function (ev) {
		ev.preventDefault();
		var row = $(this).parent().parent();
		var action = $(this).data("action");
		
		switch(action){
			case "delete":
				if(confirm("Tem a certeza que quer apagar este item do menu?")){
					var id = $(row).find("[name$='[MenuItem][id]']").val();
					$.post("/servicos/perfil/Menu/deleteMenuItem/"+id, function (data) {
						if (data.success)
							$(row).remove();
						else
							alert(data.error);
					}, 'json');
					$("#refreshMenuButton").click();
				}
				
				break;
			case "moveUp":
				// Check if parent or child
				if($(row).hasClass("parent")){
					// If parent find prev parent
					var target;
					$(".parent").each(function (i, p) {
						if ($(p).data("id") != $(row).data("id"))
							target = p;
						else
							return false
					});	
					moveRow(row,target,true);
				} else {
					var target = $(row).prev();
					if(!$(target).hasClass("parent")){
						moveRow(row,target,true);
					}
				}
				break;
			case "moveDown":
				// Check if parent or child
				if($(row).hasClass("parent")){
					// If parent find next parent
					var target;
					var found = false;
					$(".parent").each(function (i, p) {
						target = p;
						
						if (found)
							return false;
							
						if ($(p).data("id") == $(row).data("id"))
							found = true;						
					});	
					
					while($(target).next().hasClass("child"))
						target = $(target).next();
					
					moveRow(row,target,false);								
				} else {
					var target = $(row).next();
					if(!$(target).hasClass("parent") && target.length > 0){
						moveRow(row,target,false);
					}
				}
				break;
			default:
				return false;					
		}
	});
	
	$.fn.reverse = [].reverse;
	
	$(".draggable").draggable({
        helper: function (event) {
			var div = document.createElement("div");
			$(div).attr("style","border:1px solid #000;background-color:#FFF;");
			
			var clone = $(this).clone();
			$(clone).attr("style","width:100%");
			
			$(div).html(clone);
			
			return div;
		}, 
        appendTo: 'body'
	}).droppable({
        drop: function(event, ui) {
            moveRow(ui.draggable,this);
        },
		activeClass: "highlight-active",
		hoverClass: "highlight-hover",
        accept: ".draggable"
    });
	
	function moveRow(linha, alvo, beforeAlvo) {
		// Parent mudou?
		if ($(alvo).hasClass("child") && $(linha).hasClass("child") 
			&& ($(alvo).data("parent") != $(linha).data("parent")) ){
			if(!confirm("Alterar parent?")){
				return false;
			} 
		}
	
		// Actualiza parent
		if ($(linha).hasClass("child") && $(alvo).hasClass("child")) { // Ambos child
			$(linha).find("[name$='[MenuItem][parent_id]']").val($(alvo).data("parent"));
			$(linha).data("parent",$(alvo).data("parent"));
			$(linha).attr("data-parent",$(alvo).data("parent"));
		} else if ($(linha).hasClass("child") && $(alvo).hasClass("parent") && !beforeAlvo){ // Mudar para outro parent
			$(linha).find("[name$='[MenuItem][parent_id]']").val($(alvo).data("id"));
			$(linha).data("parent",$(alvo).data("id"));
			$(linha).attr("data-parent",$(alvo).data("id"));
		} else if ($(linha).hasClass("parent")){ // parent
			$(linha).find("[name$='[MenuItem][parent_id]']").val(0);
			$(linha).data("parent",0);
			$(linha).attr("data-parent",0);
			
			beforeAlvo = true;
		}
		
		// Mexe a linha
		if (beforeAlvo)
			$(alvo).before($(linha));	
		else 
			$(alvo).after($(linha));
			
			
		// Se a linha e um parent, mexe os childs tb
		if ($(linha).hasClass("parent")){
			var children = $("[data-parent='"+$(linha).data("id")+"']").reverse();
			
			$(children).each(function (i,elem) {
				$(linha).after(elem);
			});	
		}
		
		// Altera os campos order
		$(linha).parent().find("[data-parent='"+$(linha).data("parent")+"']").find("[name$='[MenuItem][ordem]']").each(function(i,elem){
			$(elem).val(parseInt(i)+1);
		}); 					
	}
	
	function addIcon(menu_id) {
		dialogas('/servicos/perfil/Menu/addIcon/' + menu_id, 'Gerir icon', 300,300);
		
	}
</script>
