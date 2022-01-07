<?php //pr($utilizador); 
?>
<style type="text/css">
  .content tr {
    border-bottom: 0px solid black;
  }

  .input_show {
    cursor: default;
  }
</style>
<h1>A minha conta</h1>
<table style="margin-bottom:40px;" class="limpo">
  </thead>
  <caption>Detalhes da conta</caption>
  <tr>
    <th style="width:200px;"></th>
    <th style="min-width:230px;"></th>

  </tr>
  <tr>
    <td><strong>Nome completo</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['nome_completo']); ?>" /></td>

  </tr>
  <tr>
    <td><strong>Nome a mostrar</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['nome_a_mostrar']); ?>" /></td>

  </tr>
  <tr>
    <td><strong>Username</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo $utilizador['username']; ?>" /></td>

  </tr>
  <tr>
    <td><strong>Email</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo $utilizador['mail']; ?>" /></td>

  </tr>
  <tr>
    <td><strong>BI</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo $utilizador['bi']; ?>" /></td>

  </tr>
  <tr>
    <td><strong>Função:</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['funcao']); ?>" /></td>

  </tr>
  <tr>
    <td><strong>Unidade</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['unidade']); ?>" /></td>

  </tr>
  <tr>
    <td><strong>Conta criada em</strong></td>
    <td><input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['data_criacao_conta']); ?>" /></td>

  </tr>
  <tr>
    <td><strong><em>Password</em> expira a</strong></td>
    <td><input type="text" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['data_expiracao_password']); ?>" /></td>

  </tr>
  <tr>
    <td><strong>Área de Ficheiros</strong></td>
    <td>


      <input type="text" class="input_show" readonly="readonly" style="width:100%;" value="<?php echo ($utilizador['armazenamento'] != NULL) ? $utilizador['armazenamento'] : ''; ?>" />

    </td>
  </tr>
  <?php if ($grupos != NULL) { ?>
    <tr>
      <td style="vertical-align:top;"><strong>Grupos</strong></td>
      <td>
        <table>
          <?php foreach ($grupos as $g) {  ?>
            <tr>
              <td><strong><?php echo $g['nome']; ?></strong></td>
              <td><?php echo $g['descricao']; ?></td>
            </tr>
          <?php } ?>
        </table>


      </td>
    </tr>
  <?php } ?>
</table>



<div id="contactos" style="float:left;width:48%">
  <table class="limpo">
    <caption>
      Contactos
    </caption>
    <tr>
      <th style="width:100px;"></th>
      <th style="width:150px;"></th>
      <th style="width:25px;"></th>
    </tr>
    <tr>
      <td><strong>Sala/Gabinete</strong></td>
      <td>
        <input type="text" name="sala" id="sala" value="<?php echo $utilizador['contactos_sala']; ?>">
      </td>
      <td>
        <button class="btnCI" onclick="changeAttribute('sala');"><i class="fa  fa-pencil"></i> </button>

    </tr>

    <tr>
      <td><strong>Extensão principal</strong></td>
      <td id="input_container_ext_principal">
        <input type="text" name="ext_principal" id="ext_principal" value="<?php echo $utilizador['contactos_extensao_principal']; ?>">
      </td>
      <td>
        <button class="btnCI" onclick="changeAttribute('ext_principal');"><i class="fa  fa-pencil"></i></button>
      </td>
    </tr>

    <tr>
      <td><strong>Extensão alternativa</strong></td>
      <td id="input_container_ext_alt">
        <input type="text" name="ext_alt" id="ext_alt" value="<?php echo $utilizador['contactos_extensao_alternativa']; ?>">

      </td>
      <td>
        <?php if ($utilizador['contactos_extensao_principal'] != NULL && trim($utilizador['contactos_extensao_principal']) != '') { ?>

          <button class="btnCI" onclick="changeAttribute('ext_alt');"><i class="fa  fa-pencil"></i></button>

        <? } else { ?>

        <?php } ?>
    </tr>
    <tr>
      <td><strong>Extensão pessoal</strong></td>
      <td id='extensao_pessoal_voip'>xxx</td>
    </tr>
    <tr>
      <td><strong>Telefone direto</strong></td>
      <td id="input_container_tlf_direto">
        <input type="text" name="tlf_direto" id="tlf_direto" value="<?php echo $utilizador['contactos_telefone_directo']; ?>">
      </td>
      <td>
        <button class="btnCI" onclick="changeAttribute('tlf_direto');"><i class="fa  fa-pencil"></i></button>
      </td>
    </tr>
    <!--<tr>
		<td><strong>Telemóvel</strong></td>
		<td id="input_container_telemovel">
		<input type="text" name="telemovel" id="telemovel" value="<?php echo $utilizador['contactos_telemovel']; ?>">
         </td>
      <td>
         <button class="btnCI btnCI-default" onclick="changeAttribute('telemovel');">
         <i class="fa  fa-pencil" style="margin-top:-4px;" ></i>
         </button>
        </td>
	</tr>    
    -->
  </table>
</div>

<div id="foto_e_cv" style="float:right;width:48%">

  <table class="limpo">
    <caption>
      Foto e CV
    </caption>
    <tr>
      <th style="width:100px;"></th>
      <th style="width:150px;"></th>
      <th style="width:25px;"></th>
      <th style="width:25px;"></th>
    </tr>
    <tr>
      <td style=""><strong>Foto</strong></td>
      <td style="" id="input_container_foto">
        <?php

        $nome = (strlen($utilizador['extra']['foto']['name']) > 15) ? substr($utilizador['extra']['foto']['name'], 0, 13) . ".." :  $utilizador['extra']['foto']['name'];

        ?>
        <a href="/servicos/perfil/Conta/showFile?id=<?php echo $utilizador['extra']['foto']['id']; ?>&tipo=foto">
          <?php echo ($nome); ?>
        </a>

      </td>
      <td id="fotoUploadContainer"></td>
      <?php if ($utilizador['extra']['foto']['id'] != NULL || $utilizador['extra']['foto']['id'] != "") { ?>
        <td>
          <input type="hidden" value="<?php echo $utilizador['extra']['foto']['id']; ?>" name="delete_foto" id="delete_foto" />
          <button class="btnCI" onclick="delete_foto('foto')"><i class="fa  fa-trash"></i> </button>
        </td>
      <?php } else {
        echo "<td></td>";
      } ?>
    <tr>
      <td style="min-width:100px;"><strong>Curriculum Vitae</strong></td>
      <td id="input_container_cv">

        <?php

        $nome = (strlen($utilizador['extra']['cv']['name']) > 15) ? substr($utilizador['extra']['cv']['name'], 0, 13) . ".." :  $utilizador['extra']['cv']['name'];

        ?>
        <a href="/servicos/perfil/Conta/showFile?id=<?php echo $utilizador['extra']['cv']['id']; ?>&tipo=cv">
          <?php echo ($nome); ?>
        </a>

      </td>
      <td id="cvUploadContainer"></td>
      <?php if ($utilizador['extra']['cv']['id'] != NULL || $utilizador['extra']['cv']['id'] != "") { ?>
        <td>
          <input type="hidden" value="<?php echo $utilizador['extra']['cv']['id']; ?>" name="delete_file" id="delete_file" />
          <button class="btnCI" onclick="delete_file('file')"><i class="fa  fa-trash"></i> </button>
        </td>
      <?php } else {
        echo "<td></td>";
      } ?>
    </tr>
  </table>
</div>

<p style="clear:both"></p>
<script type='text/javascript'>
  function delete_file(attribute) {
    var status = confirm("Tem a certeza que deseja eliminar o seu CV?");
    if (status == true) {
      var type = attribute;
      var val = $('#delete_file').val();
      $.post("/servicos/perfil/Conta/deleteAnexo", {
        "type": type,
        "val": val
      }, function(data) {
        var msg = data;
        if (data == 1) {
          alert("O CV foi removido do seu perfil.");
          reloadPage('/servicos/perfil/Conta/?refresh=1');
        } else {
          alert(data);
        }
      });
    }
  }

  function delete_foto(attribute) {
    var status = confirm("Tem a certeza que deseja eliminar a sua fotografia?");
    if (status == true) {
      var type = attribute;
      var val = $('#delete_foto').val();
      $.post("/servicos/perfil/Conta/deleteAnexo", {
        "type": type,
        "val": val
      }, function(data) {
        var msg = data;
        if (data == 1) {
          alert("A fotografia foi removida do seu perfil.");
          reloadPage('/servicos/perfil/Conta/?refresh=1');
        } else {
          alert(data);
        }
      });
    }
  }


  function changeAttribute(attribute) {
    var type = attribute;
    var val = $("#" + attribute).val();
    $.post("/servicos/perfil/Conta/edit", {
      "type": type,
      "val": val
    }, function(data) {
      var msg = data;
      if (data == 1)
        msg = 'Perfil atualizado com sucesso.';
      infoMsg(msg);
    });
  }

  $(document).ready(function() {

    fileUploadEnable('foto', 'fotoUploadContainer', 'Upload');
    fileUploadEnable('cv', 'cvUploadContainer', 'Upload');
    getExtensaoPessoalVoip()
  });

  function fileUploadEnable(tipo, div, buttonText) {

    var uploader = new qq.FileUploader({
      // pass the dom node (ex. $(selector)[0] for jQuery users)
      element: document.getElementById(div),
      // path to server-side upload script
      action: '/servicos/perfil/Conta/guardarFicheiroEAssociarAoPerfil',
      params: {
        tipo_ficheiro: tipo,
      },
      sizeLimit: 2097152,
      messages: {
        typeError: "{file} tem uma extensão inválida. Só {extensions} são permitidas.",
        sizeError: "{file} é demasiado grande, o máximo permitido é {sizeLimit}.",
        emptyError: "{file} está vazio. Escolha outro por favor.",
        onLeave: "Os ficheiros estão a ser carregados. Se sair irá perder dados."
      },
      uploadButtonText: '<i class="fa fa-upload"></i> ',
      //sizeLimit: 0, // max size   
      //minSizeLimit: 0, // min size
      onProgress: function(id, fileName, loaded, total) {
        $("#icon_upload").show('fast');
        //$("#nome_ficheiro").val('Por favor aguarde..');
      },
      onComplete: function(id, fileName, responseJSON) {
        if (responseJSON[1] == 'SUCC') {
          infoMsg('Perfil atualizado com sucesso.');

          reloadPage('/servicos/perfil/Conta');

        } else
          alert(responseJSON['error']);
      }

    });

  }

  function getExtensaoPessoalVoip() {
    console.log('ok');
    $.get("/servicos/pedidosVoip/extensoes?user=<?= $utilizador['username'] ?>&estado=2", function(data) {
      const domObject = $('#extensao_pessoal_voip')
      try {
        data = JSON.parse(data)
        if (!data || !data.id) {
          throw new Error()
        }

        domObject.html(data.id)

      } catch (e) {
        domObject.html('<i>Não atribuido</i>')
      }
    });
  }
</script>
