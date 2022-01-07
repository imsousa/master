<h4>Icon usado</h4>
<?php echo $dados_item['MenuItem']['icon']; ?>
<br />
<h4>Novo icon</h4>
<input type="text" name="new_icon" id="new_icon" />
<span id="res" stylE="padding-top:3px;">
</span>
<script type="text/javascript">

$("#new_icon").change(function() {
	$("#res").html('<i class="fa ' + $(this).val() + '"></i>');
});

$(".ui-dialog-buttonset").html('<button class="btnCI btnCI-success" onclick="changeIcon()">Alterar</button>');

function changeIcon() {
	$.post("/servicos/perfil/Menu/changeIcon", {"menu_id" : "<?php echo $dados_item['MenuItem']['id']; ?>", "new_icon": $("#new_icon").val(), 'label': '<?php echo $dados_item['MenuItem']['label']; ?>'}, function (data) {
		alert(data);
	});					
}
</script>