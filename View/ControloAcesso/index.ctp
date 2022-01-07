<h1>Controlo de Acesso</h1>
 
<table style="width:100%;" class="" cellspacing="1" cellpadding="2">
    <caption>
           &Uacute;ltimos 20 movimentos do cart&atilde;o FCUL associado &agrave; sua conta de utilizador
       </caption>
        
			
       <thead><tr><th>Data</th><th>Edificio</th><th>Porta</th></tr></thead>
		<?php
          for ($i = 0; $i < count($resultados->access_control); $i++) {
				echo '<tr>';
				echo '<td>'.$resultados->access_control[$i][2].'</td>';
				echo '<td>'.$resultados->access_control[$i][0].'</td>';
				echo '<td>'.utf8_encode($resultados->access_control[$i][1]).'</td>';
			 echo '</tr>';
		}
       ?>
</table>
				