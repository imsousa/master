<script type="text/javascript">
function gravarAlteracoesNewsletter() {
	$("#save_result").html("<img width='16' height='16' src='/servicos/common/img/ajax-loader.gif' alt='loading'/>");
	$.post('/servicos/perfil/ListasDistribuicao/gravarAlteracoes', $("#newsletter_form").serialize(), function (data) {
		if (data == 1) {
			infoMsg("Alterações gravadas com sucesso.");
		}else{
			infoMsg("<span style='color:red;'>Occoreu um erro a gravar as alterações</span>");
		}
		$("#save_result").html(' ');
	});
}
</script>
<h1>Subscrições</h1>
<form id='newsletter_form'>
		<?php
		for($it = 0; $it<count($resultados); $it++) {
			$disabled = "";
			$checked = "";
			if ($resultados[$it]['Newsletter']["mandatory"] == "1") {
				$checked = "checked";
				$disabled = "disabled";
			} else if (isset($resultados[$it]['Subs']["subscription_id"])) {
				//echo 'oi';
				$checked = "checked";
			}

			$optionsStr = "";
			$optionResult = $resultados[$it]['Options'];

			if (count($optionResult) > 1) {
					$optionsStr = "<a href='javascript:void(0);'  onclick='$(\"#opcoes_newsletter_".$resultados[$it]['Newsletter']["id"]."\").toggle();'>op&ccedil;&otilde;es</a>";
			}
			
			echo "<input class='news' id='".$resultados[$it]['Newsletter']["id"]."' name='newsletter[".$resultados[$it]['Newsletter']["id"]."]' $checked $disabled type='checkbox' value='".$resultados[$it]['Newsletter']["id"]."'> ".($resultados[$it]['Newsletter']["descricao"])." $optionsStr <br/>";
			
			if (count($optionResult) > 0) {
				echo "<div id='opcoes_newsletter_".$resultados[$it]['Newsletter']["id"]."' style='display:none;margin-left:20px;'>"; 
				
				
				for($j = 0; $j<count($optionResult); $j++){
					$checked = "";
					if (isset($resultados[$it]['Subs']["option_id"]) && $resultados[$it]['Subs']["option_id"] == $optionResult[$j]['t_newsletter_options']["id"])
						$checked = "checked";
					else if (!isset($optionResult[$j]['t_newsletter_available_options']["option_id"]) && $j == 0) {
						$checked = "checked";
					}
					echo "<input type='radio' $checked name='option[".$resultados[$it]['Newsletter']["id"]."]' value='".$optionResult[$j]['t_newsletter_options']["id"]."'> ".($optionResult[$j]['t_newsletter_options']["descricao"])."<br/>";
				}
				
				
				echo "</div>";
			}
		}
		?><br />
        </form>
        <div align="right">   
          <button class="btnCI" onclick="gravarAlteracoesNewsletter()"><i class="fa fa-save"></i> Guardar </button>
         <span id='save_result'></span> <br/> <br />
				Nota: as altera&ccedil;&otilde;es poder&atilde;o demorar at&eacute; 24 horas at&eacute terem efeito.
                </div>
         
				

<script type="text/javascript">
$(".news").click(function(){
	if($(this).is(':checked') ){
		$("#opcoes_newsletter_" + $(this).attr("id")).css("display", "block");
		$("#opcoes_newsletter_" + $(this).attr("id")).find('input[value="1"]').attr('checked', true);
	}else{
		$("#opcoes_newsletter_" + $(this).attr("id")).css("display", "none");
		$("#opcoes_newsletter_" + $(this).attr("id")).find('input[value="1"]').attr('checked', false);
	}
	
	/*if($(this).find('div').is(':visible') ) {
		alert("sta visiblel");
	}else{
		$(this).find('div').attr("style", "display:block");
	}*/
});
</script>