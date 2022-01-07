<h1>Sistema de Impressão</h1>

<table class="dados" style="table-layout: fixed; width: 100%"> 
<caption>
Listagem de impressões
</caption>
<p><b><u>Nota:</u></b> A listagem abaixo é referente ao dia anterior com histórico de 30 dias.</p>
       	<thead>
                 <tr>
                	 <th  style="width:100px;">
                      Data
                     </th>
                     <th>
                      Impressora
                     </th>
                     <th style="min-width:140px;">
                      Nome do Ficheiro
                     </th>
                      <th style="width:40px;">
                      P&aacute;g.
                      </th>
                       <th style="width:80px;">
                      P&aacute;g. Cor 
                      </th>
                      <th  style="width:40px;">
                      Valor
                     </th>
               </tr>
               </thead>
               <?php //pr($impressoes); ?>
              <?php 
               foreach($impressoes as $imp) {
               		echo '<tr>';
                   	echo '<td style="font-size:10px;">'.$imp['SistemaImpressao']['data'].'</td>';
                    //echo '<td style="font-size:10px;">'.utf8_encode($impressoes[$i]['Name']).'</td>';
                    //echo '<td style="font-size:11px;"><b>'.utf8_encode($impressoes[$i]['JobName']).'</b></td>';
					echo '<td style="font-size:10px;">'.$imp['SistemaImpressao']['impressora'].'</td>';
                    echo '<td style="font-size:11px;word-wrap:break-word;"><b>'.$imp['SistemaImpressao']['nome_ficheiro'].'</b></td>';
                    echo '<td style="font-size:10px;">'.$imp['SistemaImpressao']['paginas'].'</td>';
				    echo '<td style="font-size:10px;">'.$imp['SistemaImpressao']['paginas_cor'].'</td>';
                    echo '<td style="font-size:10px;">'.number_format($imp['SistemaImpressao']['valor'], 2, '.', '').'&euro;</td>';
                    echo '</tr>';
               }
               ?>
           </table>
        </td>
    </tr>
</table>
