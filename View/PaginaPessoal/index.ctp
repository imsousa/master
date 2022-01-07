<style type="text/css">
    textarea {
        width: 90%;
        height: 100px;
    }

    #legenda {
        color: #F00;
    }

    ul.nav>li.active>a {
        background-color: #FFF !important;
    }
</style>
<?php

$foto = NULL;
$foto_exists = FALSE;
if (isset($utilizador['extra']['foto'])) {
    $nome_ficheiro_foto = $utilizador['extra']['foto']['name'];
    if (strlen($nome_ficheiro_foto) > 35) {
        $nome_ficheiro_foto = substr($nome_ficheiro_foto, 0, 35) . "..";
    }
    $foto = '<span style="color: rgba(44, 63, 177, 1);">' . $nome_ficheiro_foto . ' </span>';
    $foto_exists = TRUE;
} else
    $foto = '<font color="red">Não definido</font>';

$cv = NULL;
$cv_exists = FALSE;
if (isset($utilizador['extra']['cv'])) {
    $nome_ficheiro_cv = $utilizador['extra']['cv']['name'];
    if (strlen($nome_ficheiro_cv) > 35) {
        $nome_ficheiro_cv = substr($nome_ficheiro_cv, 0, 35) . "..";
    }
    //$cv = '<a target="blank" style="color: rgba(44, 63, 177, 1);" href="/servicos/perfil/PaginaPessoal/showFile/?id='.$utilizador['extra']['cv']['id'].'&tipo=cv">'.$nome_ficheiro_cv.'</a>';  	
    $cv = '<a target="blank" style="color: rgba(44, 63, 177, 1);" href="/servicos/perfil/PaginaPessoal/showFile/?id=' . $utilizador['extra']['cv']['id'] . '&tipo=cv&user=' . $utilizador['mail'] . '">' . $nome_ficheiro_cv . '</a>';
    $cv_exists = TRUE;
} else
    $cv = '<font color="red">Não definido</font>';

?>

<script src="/misc/js/fileuploader.js"></script>
<link href="/misc/css/fileuploader.css" rel="stylesheet" type="text/css" />

<script type='text/javascript'>
    //Conteúdos

    /*Ficheiros*/
    function fileUploadEnable(file_type) {
        var file1 = new qq.FileUploader({
            params: {
                type: file_type
            },
            element: document.getElementById('o_meu_ficheiro_' + file_type),
            action: '/servicos/perfil/PaginaPessoal/edit',
            onComplete: function(id, fileName, responseJSON) {
                if (responseJSON[1] == 'SUCC') {
                    alert("Ficheiro foi anexado ao seu perfil.");
                    reloadPage('/servicos/perfil/PaginaPessoal/index');
                } else
                    alert("Falhou o Upload. Tamanho máximo: 2Mb.");
                //alert(responseJSON['error']);
            }
        });
    }

    function editarFile(type) {
        $("#input_container_" + type).html("<div id=\"o_meu_ficheiro_" + type + "\" style=\"width:150px;padding-top:11px;\">Teste<noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div><div id=\"resposta_upload_" + type + "\"><noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div>");
        if (type == 'foto') {
            $("#input_container_foto").append("Tamanho recomendado: 220px x 210px.");
            <?php if ($foto_exists) { ?>
                $("#input_container_foto").append(" (<a onclick=\"apagarFicheiro('foto_remove');\">Apagar</a>)");
            <?php } ?>
        }
        <?php if ($cv_exists) { ?>
            if (type == 'cv') {
                $("#input_container_cv").append(" (<a onclick=\"apagarFicheiro('cv_remove');\">Apagar</a>)");
            }
        <?php } ?>
        fileUploadEnable(type);
        $("#edit_cancel_" + type).html('<button class="btnCI btnCI-danger" onclick="fecharFile(\'' + type + '\');"><i class="fa  fa-close" style="margin-top:-4px;" > </i> Cancelar</button></li>');
    }

    function fecharFile(type) {
        $("#input_container_" + type).html($("#temp_" + type).val());
        $("#save_" + type).html('');
        $("#edit_cancel_" + type).html('<button class="btnCI btnCI-default" onclick="editarFile(\'' + type + '\');"><i class="fa  fa-edit" style="margin-top:-4px;" > </i> Editar</button></li>');
    }

    function apagarFicheiro(tipo) {
        $.post('/servicos/perfil/PaginaPessoal/edit', {
            "type": tipo
        }, function(data) {
            if (data == 1) {
                alert("Dados alterados com sucesso. Os dados podem demorar até cinco minutos a actualizar.");
                reloadPage('/servicos/perfil/PaginaPessoal/?refresh=1');
            } else {
                alert("Não foi possível alterar os dados.");
            }
        });

    }



    /*Atributos normais*/
    function editar(type) {
        $("#current_" + type).hide();
        $("#temp_" + type).show();
        $("#save_" + type).html('<button class="btnCI btnCI-success" onclick="salvar(\'' + type + '\');"><i class="fa  fa-check" style="margin-top:-4px;" > </i> Guardar</button></li>');
        $("#edit_cancel_" + type).html('<button class="btnCI btnCI-danger" onclick="fechar(\'' + type + '\');"><i class="fa  fa-close" style="margin-top:-4px;" > </i> Cancelar</button></li>');
    }

    function salvar(type) {
        var erros = 0;
        var input_val = $("#temp_" + type).val();
        input_val = input_val.replace(/\r?\n/g, '<br>');
        $.post('/servicos/perfil/PaginaPessoal/edit', {
            "type": type,
            "val": input_val
        }, function(data) {
            if (data == 1) {
                alert("Dados alterados com sucesso. Os dados podem demorar até cinco minutos a actualizar.");
                reloadPage('/servicos/perfil/PaginaPessoal/?refresh=1');
            } else {
                alert("Não foi possível alterar os dados.");
            }
        });
    }

    function fechar(type) {
        $("#temp_" + type).hide();
        $("#current_" + type).show();
        $("#save_" + type).html('');
        $("#edit_cancel_" + type).html('<button class="btnCI btnCI-default" onclick="editar(\'' + type + '\');"><i class="fa  fa-edit" style="margin-top:-4px;" > </i> Editar</button></li>');
    }

    function escolherEmail() {
        if (confirm('Tem a certeza que deseja selecionar este email para apresentação pública?')) {
            $.post('/servicos/perfil/PaginaPessoal/edit', {
                "type": 'email_publico',
                "val": $("#email_publico").val()
            }, function(data) {
                var msg = data;
                if (data == 1) {
                    msg = 'Email atualizado com sucesso.';
                    reloadPage('/servicos/perfil/PaginaPessoal/?refresh=1');
                } else
                    alert("Não foi possível alterar os dados.");

            });
        }
    }

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
                    alert("Removeu o seu CV do seu perfil.");
                    reloadPage('/servicos/perfil/PaginaPessoal/?refresh=1');
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
                    alert("A fotografia foi removida do seu perfil.|");
                    reloadPage('/servicos/perfil/PaginaPessoal/?refresh=1');
                } else {
                    alert(data);
                }
            });
        }
    }
</script>
<div id="browserDesactualizado" style="display:none">
</div>

<input type="hidden" id="temp_foto" value='<?php echo $foto; ?>' />
<input type="hidden" id="temp_cv" value='<?php echo $cv; ?>' />

<!-- Query que separa a visita/consulta da edição de dados -->

<?php if ($visita) { //TRATA-SE DE UM UTILIZADOR A VISITAR UMA PÁGINA 
?>



    <!-- SISTEMAS DE INVESTIGAÇÃO -->
    <?php
    $orcid = '';
    $researcherid = '';
    $scopusid = '';
    $google = '';

    if (isset($utilizador['researcher_sys1_type_id']) && $utilizador['researcher_sys1_type_id'] != '') {
        if ($utilizador['researcher_sys1_type_id'] == 1) { //ORCID  
            if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '')
                $orcid = "http://orcid.org/" . $utilizador['researcher_sys1_id'];
        } else if ($utilizador['researcher_sys1_type_id'] == 2) { //RESEARCHER ID  
            if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '')
                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys1_id'];
        } else if ($utilizador['researcher_sys1_type_id'] == 3) { //SCOPUS ID 
            if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '')
                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys1_id'];
        } else if ($utilizador['researcher_sys1_type_id'] == 4) { //GOOGLE 
            if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '')
                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys1_id'];
        }
    }

    if (isset($utilizador['researcher_sys2_type_id']) && $utilizador['researcher_sys2_type_id'] != '') {
        if ($utilizador['researcher_sys2_type_id'] == 1) { //ORCID 
            if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '')
                $orcid = "http://orcid.org/" . $utilizador['researcher_sys2_id'];
        } else if ($utilizador['researcher_sys2_type_id'] == 2) { //RESEARCHER ID 
            if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '')
                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys2_id'];
        } else if ($utilizador['researcher_sys2_type_id'] == 3) { //SCOPUS ID 
            if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '')
                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys2_id'];
        } else if ($utilizador['researcher_sys2_type_id'] == 4) { //GOOGLE 
            if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '')
                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys2_id'];
        }
    }

    if (isset($utilizador['researcher_sys3_type_id']) && $utilizador['researcher_sys3_type_id'] != '') {
        if ($utilizador['researcher_sys3_type_id'] == 1) { //ORCID
            if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '')
                $orcid = "http://orcid.org/" . $utilizador['researcher_sys3_id'];
        } else if ($utilizador['researcher_sys3_type_id'] == 2) { //RESEARCHER ID
            if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '')
                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys3_id'];
        } else if ($utilizador['researcher_sys3_type_id'] == 3) { //SCOPUS ID 
            if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '')
                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys3_id'];
        } else if ($utilizador['researcher_sys3_type_id'] == 4) { //GOOGLE 
            if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '')
                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys3_id'];
        }
    }

    if (isset($utilizador['researcher_sys4_type_id']) && $utilizador['researcher_sys4_type_id'] != '') {
        if ($utilizador['researcher_sys4_type_id'] == 1) { //ORCID
            if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '')
                $orcid = "http://orcid.org/" . $utilizador['researcher_sys4_id'];
        } else if ($utilizador['researcher_sys4_type_id'] == 2) { //RESEARCHER ID
            if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '')
                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys4_id'];
        } else if ($utilizador['researcher_sys4_type_id'] == 3) { //SCOPUS ID 
            if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '')
                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys4_id'];
        } else if ($utilizador['researcher_sys4_type_id'] == 4) { //GOOGLE 
            if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '')
                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys4_id'];
        }
    } ?>

    <!-- GBNT -->
    <h1><?php echo utf8_decode($utilizador['nome_a_mostrar']); ?></h1>

    <style type="text/css">
        .perfil_titulo {
            color: rgba(44, 63, 177, 1);

        }

        .perfil_subtitulo {
            color: rgba(44, 63, 177, 1);
            font-size: 28px;
            line-height: 36px;
            margin-bottom: 20px;
        }

        .perfil_h3_alt {

            font-size: 16px !important;
            line-height: 1em;
            margin-bottom: 15px;
        }

        .texto_com_margem {
            padding-left: 20px;
        }

        .perfil_dep_uni {
            color: black;
            margin-bottom: 15px;
            margin-left: 20px;
        }

        .perfil_span {
            color: #999;
            display: inline-block;
            line-height: 1.6em;
            margin-left: 20px;
            margin-right: 20px;

        }
    </style>


    <?php if ($foto_exists) { ?>
        <img border="1" width="240" style="float:left; margin-right:40px;" src="data:image/jpeg;base64, <?php echo base64_encode($utilizador['extra']['foto']['content']) ?>" typeof="foaf:Image" />
    <?php } ?>
    <div id="perfil_cont" style="float:left;max-width:440px;">
        <h3 class="perfil_titulo" style="margin-top: 0px !important;">Contactos</h3>
        <?php if (isset($utilizador['unidade']) && $utilizador['unidade'] != '') { ?>
            <span class="perfil_dep_uni"><?php echo utf8_decode($utilizador['unidade']); ?></span><br><br>
        <?php } ?>

        <?php if (isset($utilizador['contactos_sala']) && $utilizador['contactos_sala'] != '') { ?>
            <span class="perfil_span">Sala/Gabinete</span> <?php echo $utilizador['contactos_sala']; ?> <br>
        <?php } ?>

        <?php if (isset($utilizador['contactos_extensao_principal']) && $utilizador['contactos_extensao_principal'] != '') { ?>
            <span class="perfil_span">Ext. Principal</span> <?php echo $utilizador['contactos_extensao_principal']; ?>
        <?php } ?>
        <?php if (isset($utilizador['contactos_extensao_alternativa']) && $utilizador['contactos_extensao_alternativa'] != ' ') { ?>
            <span class="perfil_span">Ext. Alt</span> <?php echo $utilizador['contactos_extensao_alternativa']; ?>
        <?php } ?><br>

        <?php if (isset($utilizador['contactos_telefone_directo']) && $utilizador['contactos_telefone_directo'] != '') { ?>
            <span class="perfil_span">Telefone Direto</span> <?php echo $utilizador['contactos_telefone_directo']; ?><br>
        <?php } ?>

        <div id='divExtPessoalVoip' style="display: none;">
            <span class="perfil_span">Extensão pessoal</span>
            <span id='extPessoal'></span>
        </div>

        <?php if (isset($utilizador['mail']) && $utilizador['mail'] != '') { ?>
            <span class="perfil_span">Email</span>
            <?php if (isset($utilizador['email_publico']) && $utilizador['email_publico'] != NULL) {
                $new_mail = str_ireplace(
                    '@fc.ul.pt',
                    '@ciencias.ulisboa.pt',
                    $utilizador['email_publico']
                );
            ?>
                <a href="mailto:<?php echo $new_mail; ?>"><?php echo $new_mail; ?></a><br>
            <?php } else {
                /* mail de ciencias aon inves de fc.ul.pt */
                $new_mail = str_ireplace(
                    '@fc.ul.pt',
                    '@ciencias.ulisboa.pt',
                    $utilizador['mail']
                );
            ?>
                <a href="mailto:<?php echo $new_mail; ?>"><?php echo $new_mail; ?></a><br>
            <?php } ?>
        <?php } ?>
        <?php if (isset($utilizador['pagina_pessoal']) && $utilizador['pagina_pessoal'] != '') { ?>
            <?php
            function addhttp($url)
            {
                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                    $url = "http://" . $url;
                }
                return $url;
            }
            ?>
            <span class="perfil_span"><a href="<?php echo addhttp($utilizador['pagina_pessoal']); ?>" target="_new">Página Pessoal</a></span>
        <?php } ?>
    </div>
    <div style=" clear: left;"></div>



    <?php if ((isset($utilizador['carreira']) && $utilizador['carreira'] != '') || (isset($utilizador['categoria']) && $utilizador['categoria'] != '')) { ?>
        <hr />
        <?php if (isset($utilizador['carreira']) && $utilizador['carreira'] != '') { ?>
            <span class="perfil_span">Carreira</span> <?php echo $utilizador['carreira']; ?><br>
        <?php } ?>
        <?php if (isset($utilizador['categoria']) && $utilizador['categoria'] != '') { ?>
            <span class="perfil_span">Categoria</span> <?php echo $utilizador['categoria']; ?><br>
        <?php } ?>
    <?php } ?>


    <?php if ($researcherid != '' || $orcid != '' || $google != '' || $google != '') { ?>
        <hr />
        <h3 class="perfil_h3_alt">Indicadores</h3>
        <?php if ($researcherid != '') { ?>
            <span class="perfil_span"><?php echo "<a href='" . $researcherid . "' target='_blank'>ResearcherID</a>"; ?></span><br>
        <?php } ?>
        <?php if ($orcid != '') { ?>
            <span class="perfil_span"><?php echo "<a href='" . $orcid . "' target='_blank'>Orcid</a>"; ?></span><br>
        <?php } ?>
        <?php if ($scopusid != '') { ?>
            <span class="perfil_span"><?php echo "<a href='" . $scopusid . "' target='_blank'>Scopus</a>"; ?></span><br>
        <?php } ?>
        <?php if ($google != '') { ?>
            <span class="perfil_span"><?php echo "<a href='" . $google . "' target='_blank'>Google Scholar</a>"; ?></span>
        <?php } ?>
    <?php } ?>


    <?php if ((isset($utilizador['palavraschave']) && $utilizador['palavraschave'] != '') || (isset($utilizador['keywords']) && $utilizador['keywords'] != '')) { ?>
        <hr />
        <?php if (isset($utilizador['palavraschave']) && $utilizador['palavraschave'] != '') {
            $Palavras = explode(';', $utilizador['palavraschave']); ?>
            <h3 class="perfil_h3_alt">Palavras Chave</h3>
            <div class="ru-section-content section-list">
                <ul class="section-content-keywords">
                    <?php for ($i = 0; $i < count($Palavras); $i++) {
                        echo "<li>" . $Palavras[$i] . "</li>";
                    } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (isset($utilizador['keywords']) && $utilizador['keywords'] != '') {
            $Keywords = explode(';', $utilizador['keywords']); ?>
            <h3 class="perfil_h3_alt">Keywords</h3>
            <div class="ru-section-content section-list">
                <ul class="section-content-keywords">
                    <?php for ($i = 0; $i < count($Keywords); $i++) {
                        echo "<li>" . $Keywords[$i] . "</li>";
                    } ?>
                </ul>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if ((isset($utilizador['cv_resumido']) && $utilizador['cv_resumido'] != '') || $cv_exists) { ?>
        <hr />
        <?php if (isset($utilizador['cv_resumido']) && $utilizador['cv_resumido'] != '') { ?>
            <div class="perfil_subtitulo">Currículo Resumido</div>
            <div class="texto_com_margem ">
                <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['cv_resumido']);
                foreach ($arrayParagrafos as $a) {
                    echo "<p>" . $a . "</p>";
                } ?>
                <p><?php //echo $utilizador['cv_resumido']; 
                    ?></p>
            </div>
        <?php } ?>
        <?php if ($cv_exists) { ?>
            <div class="texto_com_margem ">
                <?php echo "<a class='doc-download btnCI' href='/servicos/perfil/PaginaPessoal/showFile/?id=" . $utilizador['extra']['cv']['id'] . "&tipo=cv&user=" . $utilizador['mail'] . "'>Download currículo completo <i class='fa fa-arrow-down'></i></a>"; ?>
            </div>
        <?php } ?>

    <?php } ?>


    <?php if (isset($utilizador['interesses_cientificos']) && $utilizador['interesses_cientificos'] != '') { ?>
        <hr />

        <div class="perfil_subtitulo">Interesses Científicos</div>
        <div class="texto_com_margem ">
            <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['interesses_cientificos']);
            foreach ($arrayParagrafos as $a) {
                echo "<p>" . $a . "</p>";
            } ?>
        </div>
    <?php } ?>



    <?php if (isset($utilizador['scientific_interests']) && $utilizador['scientific_interests'] != '') { ?>
        <hr />

        <div class="perfil_subtitulo">Scientific Interests</div>
        <div class="texto_com_margem ">
            <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['scientific_interests']);
            foreach ($arrayParagrafos as $a) {
                echo "<p>" . $a . "</p>";
            } ?>
        </div>
    <?php } ?>



    <?php if ((isset($utilizador['pub1']) && $utilizador['pub1'] != '') ||
        (isset($utilizador['pub2']) && $utilizador['pub2'] != '') ||
        (isset($utilizador['pub3']) && $utilizador['pub3'] != '') ||
        (isset($utilizador['pub4']) && $utilizador['pub4'] != '') ||
        (isset($utilizador['pub5']) && $utilizador['pub5'] != '')
    ) { ?>
        <hr />

        <div class="perfil_subtitulo">Publicações selecionadas</div>
        <div class="ru-section-content pp-selected-publications">
            <ul>
                <?php if (isset($utilizador['pub1']) && $utilizador['pub1'] != '') { ?>
                    <li><?php echo $utilizador['pub1']; ?></li>
                <?php } ?>
                <?php if (isset($utilizador['pub2']) && $utilizador['pub2'] != '') { ?>
                    <li><?php echo $utilizador['pub2']; ?></li>
                <?php } ?>
                <?php if (isset($utilizador['pub3']) && $utilizador['pub3'] != '') { ?>
                    <li><?php echo $utilizador['pub3']; ?></li>
                <?php } ?>
                <?php if (isset($utilizador['pub4']) && $utilizador['pub4'] != '') { ?>
                    <li><?php echo $utilizador['pub4']; ?></li>
                <?php } ?>
                <?php if (isset($utilizador['pub5']) && $utilizador['pub5'] != '') { ?>
                    <li><?php echo $utilizador['pub5']; ?></li>
                <?php } ?>
            </ul>
            <p><a href="https://biblios.ciencias.ulisboa.pt/autor/<?php echo $utilizador['username']; ?>" class="btnCI btnCI-default" target="_blank">Ver todas as Publicações</a></p>
        </div>
    <?php } ?>












<?php
    //end vistiante
} else { //TRATA-SE DE UM UTILIZADOR A EDITAR OS SEUS DADOS 
?>

    <h1>A sua página de Ciências</h1>
    <hr />
    <?php /* if(isset($utilizador['mensagem'])) { ?>
	<div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
	<h4 class="alert-heading">Atenção!</h4>
	<ul><li><?php echo $utilizador['mensagem']; ?></li></ul>
	</div>
	<?php } */ ?>
    <div class="alert alert-info" style=" margin-top:10px; margin-bottom:10px">
        <h4 class="alert-heading">Atenção!</h4>
        <ul>
            <li>Esta é a sua página pessoal no site da FCUL, onde poderá editar a sua <b><u>informação pública</u></b>.</li>
        </ul>
    </div>

    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#dados" data-toggle="tab"><i class="icon-user"></i>&nbsp;Dados Gerais</a>
            </li>
            <li class>
                <a href="#preview" data-toggle="tab"><i class="icon-list-alt"></i>&nbsp;Preview da página</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="dados">
                <!-- Backoffice - Inserção de dados -->
                <strong>Legenda : (<span id="legenda">*</span>) máximo 2500 caracteres</strong> (inclui espaços entre caracteres) <br /><br />
                <fieldset>
                    <legend>Dados pessoais</legend>

                    <table class="limpo" style="100%">
                        <tr>
                            <th style="width:150px;"></th>
                            <th style="width:250px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:100px;"></th>
                        </tr>
                        <tr>
                            <td><strong>Nome completo</strong></td>
                            <td colspan="3"><?php echo utf8_decode($utilizador['nome_completo']); ?></td>
                        </tr>
                        <!-- <tr>
                            <td><strong>Nome a mostrar</strong></td>
                            <td colspan="3"><?php // echo utf8_decode($utilizador['nome_a_mostrar']); 
                                            ?></td>
                       </tr> -->
                        <tr>
                            <td><strong>Nome a mostrar</strong></td>
                            <td id="input_container_displayname">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_displayname" value='<?php echo utf8_decode($utilizador['nome_a_mostrar']); ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_displayname"><?php echo utf8_decode($utilizador['nome_a_mostrar']); ?></div>

                            </td>
                            <td id="save_displayname"> </td>
                            <td id="edit_cancel_displayname" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('displayname');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Username</strong></td>
                            <td colspan="3"><?php echo $utilizador['username']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <?php $totalAlias = count($utilizador['aliases']); //EXISTEM ALIAS, É POSSIVEL ESCOLHER QUE EMAIL APARECE
                            if ($totalAlias > 0) { ?>
                                <td>
                                    <select id="email_publico" class="span4">
                                        <?php if ($utilizador['email_publico']) { ?>
                                            <option selected="selected" value="<?php $utilizador['email_publico']; ?>"><?php echo $utilizador['email_publico']; ?></option>
                                            <option value="<?php echo $utilizador['mail']; ?>"><?php echo $utilizador['mail']; ?></option>
                                        <?php } else { ?>
                                            <option selected="selected" value="<?php echo $utilizador['mail']; ?>"><?php echo $utilizador['mail']; ?></option>
                                        <?php } ?>
                                        <?php for ($i = 0; $i < $totalAlias; $i++) { ?>
                                            <option value="<?php echo $utilizador['aliases'][$i]; ?>"><?php echo $utilizador['aliases'][$i]; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td></td>
                                <td style="text-align:right !important"><button class="btnCI" onclick="escolherEmail()"><i class="fa fa-save"></i> Guardar</button></td>
                            <?php } else { ?>
                                <td colspan="3"><?php echo $utilizador['mail']; ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="4" style="background-color:#DDD !important"><strong>Nota</strong>: os endereços <i><strong>@fc.ul.pt</strong></i> serão apresentados no formato <i><strong>@ciencias.ulisboa.pt</strong></i></td>
                        </tr>

                        <tr>
                            <td><strong>Carreira:</strong></td>
                            <td colspan="3"><?php echo $utilizador['carreira']; ?></td>
                        </tr>
                        <?php if ($utilizador['categoria'] != "") { ?>
                            <tr>
                                <td><strong>Categoria:</strong></td>
                                <td colspan="3"><?php echo $utilizador['categoria']; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><strong>Unidade</strong></td>
                            <td colspan="3"><?php echo utf8_decode($utilizador['unidade']); ?></td>
                        </tr>
                    </table>
                </fieldset>
                <br />
                <fieldset>
                    <legend>Fotografia</legend>
                    <table class="limpo" style="100%">
                        <tr>
                            <th style="width:125px;"></th>
                            <th style="width:250px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:25px;"></th>

                        </tr>
                        <tr>
                            <td><strong>Foto</strong></td>
                            <td id="input_container_foto"><?php echo $foto; ?> </td>
                            <?php if ($foto_exists) { ?>
                                <td id="display_container_foto"><img border="1" height="75" width="75" src="data:image/jpeg;base64, <?php echo base64_encode($utilizador['extra']['foto']['content']) ?>" typeof="foaf:Image" /></td>
                            <?php } else { ?>
                                <td></td>
                            <?php } ?>
                            <td id="edit_cancel_foto" style="vertical-align:right;min-width:100px;">
                                <button class="btnCI btnCI-default" onclick="editarFile('foto');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                            </td>
                            <?php if ($utilizador['extra']['foto']['id'] != NULL || $utilizador['extra']['foto']['id'] != "") { ?>
                                <td>
                                    <input type="hidden" value="<?php echo $utilizador['extra']['foto']['id']; ?>" name="delete_foto" id="delete_foto" />
                                    <button class="btnCI" onclick="delete_foto('foto')"><i class="fa  fa-trash"></i> </button>
                                </td>
                            <?php } else {
                                echo "<td></td>";
                            } ?>
                    </table>
                </fieldset>
                <br />
                <fieldset>
                    <legend>Detalhes extra</legend>
                    <table class="limpo" style="100%">
                        <tr>
                            <th style="width:125px;"></th>
                            <th style="width:250px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:25px;"></th>
                        </tr>
                        <tr>
                            <td><strong>Sala/Gabinete</strong></td>
                            <td id="input_container_sala">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_sala" value='<?php echo $utilizador['contactos_sala']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_sala"><?php echo $utilizador['contactos_sala']; ?></div>

                            </td>
                            <td id="save_sala"> </td>
                            <td id="edit_cancel_sala" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('sala');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Extensão principal</strong></td>
                            <td id="input_container_ext_principal">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_ext_principal" value='<?php echo $utilizador['contactos_extensao_principal']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_ext_principal"><?php echo $utilizador['contactos_extensao_principal']; ?></div>

                            </td>
                            <td id="save_ext_principal"></td>
                            <td id="edit_cancel_ext_principal" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('ext_principal');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Extensão alternativa</strong></td>
                            <td id="input_container_ext_alt">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_ext_alt" value='<?php echo $utilizador['contactos_extensao_alternativa']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_ext_alt"><?php echo $utilizador['contactos_extensao_alternativa']; ?></div>

                            </td>
                            <td id="save_ext_alt"></td>
                            <?php if ($utilizador['contactos_extensao_principal'] != NULL && trim($utilizador['contactos_extensao_principal']) != '') { ?>
                                <td id="edit_cancel_ext_alt" style="vertical-align:right;">
                                    <button class="btnCI btnCI-default" onclick="editar('ext_alt');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                                </td>
                            <? } else { ?>
                                <td id="edit_cancel_ext_alt" style="vertical-align:right;"></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td><strong>Telefone direto</strong></td>
                            <td id="input_container_tlf_direto">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_tlf_direto" value='<?php echo $utilizador['contactos_telefone_directo']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_tlf_direto"><?php echo $utilizador['contactos_telefone_directo']; ?></div>
                            </td>
                            <td id="save_tlf_direto"></td>
                            <td id="edit_cancel_tlf_direto" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('tlf_direto');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Extensão pessoal</strong></td>
                            <td id='extPessoalVoip'></td>

                            <script>
                                console.log('result');
                                $.get('/servicos/pedidosVoip/extensoes/search?utilizador=<?= $utilizador['username'] ?>', (data) => {
                                    console.log(data);
                                    data = JSON.parse(data)

                                    if (data.length == 1) {
                                        data = data[0]

                                        $('#extPessoalVoip').html(data.id)
                                    }
                                })
                            </script>
                        </tr>
                        <?php /* TELEMÓVEL DEIXA DE SER DISPONIBILIZADO
                    <tr>
                        <td><strong>Telemóvel</strong></td>
                        <td id="input_container_telemovel"><?php echo $utilizador['contactos_telemovel']; ?></td>
                        <td id="save_telemovel"> </td>
                        <td id="edit_cancel_telemovel"  style="vertical-align:right;"><!--<a style="width:90px;margin:0px;display:inline-block;" class="white_grey_1" onclick="javascript:editar('telemovel',<?php// echo $utilizador['contactos_telemovel']; ?>);" ><img src="http://static.fc.ul.pt//www/img/icons/Cinza_24x24/edit_24_24_32.png">Editar</a>-->
                        <button class="btnCI btnCI-default" onclick="editar('telemovel','<?php echo $utilizador['contactos_telemovel']; ?>');"><i class="fa  fa-edit" style="margin-top:-4px;" > </i> Editar</button></td>
                    </tr> */ ?>
                        <tr>
                            <td><strong>Página Pessoal</strong></td>
                            <td id="input_container_pagina_pessoal">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pagina_pessoal" value='<?php echo $utilizador['pagina_pessoal']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pagina_pessoal"><?php echo $utilizador['pagina_pessoal']; ?></div>
                            </td>
                            <td id="save_pagina_pessoal"> </td>
                            <td id="edit_cancel_pagina_pessoal" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pagina_pessoal');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>(<font color="#FF0000">*</font>)Currículo Resumido</strong></td>
                            <td id="input_container_cv_resumido">

                                <!-- TEXTAREA HIDDEN -->
                                <textarea style="display:none;" id="temp_cv_resumido" maxlength="2500"><?php echo $utilizador['cv_resumido']; ?></textarea>

                                <!-- DADOS QUE APARECEM DE INICIO -->
                                <div id="current_cv_resumido">
                                    <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['cv_resumido']);
                                    foreach ($arrayParagrafos as $a) {
                                        echo "<p>" . $a . "</p>";
                                    } ?>

                                </div>
                            </td>
                            <td id="save_cv_resumido"></td>
                            <td id="edit_cancel_cv_resumido" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('cv_resumido');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>
                                </strong></td>
                            <td id="input_container_cv" colspan="2"><?php echo $cv; ?></td>
                            <td id="edit_cancel_cv" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editarFile('cv');"><i class="fa  fa-edit" style="margin-top:-4px;"> </i>Editar</button>
                            </td>
                            <?php if ($utilizador['extra']['cv']['id'] != NULL || $utilizador['extra']['cv']['id'] != "") { ?>
                                <td>
                                    <input type="hidden" value="<?php echo $utilizador['extra']['cv']['id']; ?>" name="delete_file" id="delete_file" />
                                    <button class="btnCI" onclick="delete_file('file')"><i class="fa  fa-trash"></i> </button>
                                </td>
                            <?php } else {
                                echo "<td></td>";
                            } ?>
                        </tr>
                        <tr>
                            <td><strong>(<font color="#FF0000">*</font>)Interesses Científicos</strong></td>
                            <td id="input_container_interesses_cientificos">

                                <!-- TEXTAREA HIDDEN -->
                                <textarea style="display:none;" id="temp_interesses_cientificos" maxlength="2500"><?php echo $utilizador['interesses_cientificos']; ?></textarea>

                                <!-- DADOS QUE APARECEM DE INICIO -->
                                <div id="current_interesses_cientificos">
                                    <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['interesses_cientificos']);
                                    foreach ($arrayParagrafos as $a) {
                                        echo "<p>" . $a . "</p>";
                                    } ?>

                                </div>
                            </td>

                            <td id="save_interesses_cientificos"></td>
                            <td id="edit_cancel_interesses_cientificos" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('interesses_cientificos');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>(<font color="#FF0000">*</font>)Scientific Interests</strong></td>
                            <td id="input_container_scientific_interests">

                                <!-- TEXTAREA HIDDEN -->
                                <textarea style="display:none;" id="temp_scientific_interests" maxlength="2500"><?php echo $utilizador['scientific_interests']; ?></textarea>

                                <!-- DADOS QUE APARECEM DE INICIO -->
                                <div id="current_scientific_interests">
                                    <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['scientific_interests']);
                                    foreach ($arrayParagrafos as $a) {
                                        echo "<p>" . $a . "</p>";
                                    } ?>

                                </div>
                            </td>
                            <td id="save_scientific_interests"></td>
                            <td id="edit_cancel_scientific_interests" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('scientific_interests');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><strong><u>Publicações Selecionadas (máximo 5)</u></strong></td>
                        </tr>
                        <tr>
                            <td><strong>1ª Publicação</strong></td>
                            <td id="input_container_pub1">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pub1" value='<?php echo $utilizador['pub1']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pub1"><?php echo $utilizador['pub1']; ?></div>
                            </td>
                            <td id="save_pub1"> </td>
                            <td id="edit_cancel_pub1" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pub1');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>2ª Publicação</strong></td>
                            <td id="input_container_pub2">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pub2" value='<?php echo $utilizador['pub2']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pub2"><?php echo $utilizador['pub2']; ?></div>
                            </td>
                            <td id="save_pub2"> </td>
                            <td id="edit_cancel_pub2" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pub2');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>3ª Publicação</strong></td>
                            <td id="input_container_pub3">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pub3" value='<?php echo $utilizador['pub3']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pub3"><?php echo $utilizador['pub3']; ?></div>
                            </td>
                            <td id="save_pub3"> </td>
                            <td id="edit_cancel_pub3" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pub3');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>4ª Publicação</strong></td>
                            <td id="input_container_pub4">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pub4" value='<?php echo $utilizador['pub4']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pub4"><?php echo $utilizador['pub4']; ?></div>
                            </td>
                            <td id="save_pub4"> </td>
                            <td id="edit_cancel_pub4" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pub4');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>5ª Publicação</strong></td>
                            <td id="input_container_pub5">

                                <!--HIDDEN FIELD-->
                                <input type="text" style="display:none;" id="temp_pub5" value='<?php echo $utilizador['pub5']; ?>' />

                                <!--O QUE SURGE INICIALMENTE -->
                                <div id="current_pub5"><?php echo $utilizador['pub5']; ?></div>
                            </td>
                            <td id="save_pub5"> </td>
                            <td id="edit_cancel_pub5" style="vertical-align:right;">
                                <button class="btnCI btnCI-default" onclick="editar('pub5');"><i class="fa  fa-edit" style="margin-top:-4px;"></i> Editar</button>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Detalhes Census</legend>
                    <div class="alert alert-danger" style=" margin-top:10px; margin-bottom:10px">
                        <h4 class="alert-heading">Atenção!</h4>
                        <ul>
                            <li>Os detalhes dos Indicadores, Palavras Chave e Keywords são definidas de forma centralizada no serviço <a href="http://www.fc.ul.pt/census/" target="_new">Census</a>.</li>
                            <li>A inserção/atualização destes conteúdos pode ser solicitada através do email: <a href="mailto:falopes@ciencias.ulisboa.pt">falopes@ciencias.ulisboa.pt</a></li>
                        </ul>
                    </div>
                    <!-- INDICADORES -->
                    <?php
                    $orcid = '';
                    $researcherid = '';
                    $scopusid = '';
                    $google = '';
                    ?>
                    <table class="limpo" style="100%">
                        <tr>
                            <th colspan="2" style="min-width:250px;"></th>
                            <th style="width:225px;"></th>
                            <th style="width:225px;"></th>
                        </tr>
                        <tr>
                            <th colspan="2">Indicadores</th>
                            <th>Código</th>
                            <th>URL</th>
                        </tr>
                        <!-- 1 Sistema -->
                        <tr><?php if (isset($utilizador['researcher_sys1_type_id']) && $utilizador['researcher_sys1_type_id'] != '') {
                                if ($utilizador['researcher_sys1_type_id'] == 1) { //ORCID 
                            ?>
                        <tr>
                            <td>#1</td>
                            <td><?php echo "<strong>" . $utilizador['researcher_sys1_designacao'] . "</strong>"; ?></td>
                            <?php if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '') {
                                        $orcid = "http://orcid.org/" . $utilizador['researcher_sys1_id']; ?>
                                <td><?php echo $utilizador['researcher_sys1_id']; ?> </td>
                                <td><?php echo "<a href='" . $orcid . "' target='_new'>" . $orcid . "</a>"; ?></td>
                            <?php } else { ?>
                                <td>Código não definido</td>
                                <td>URL não definido</td>
                            <?php } ?>
                        </tr>
                    <?php } else if ($utilizador['researcher_sys1_type_id'] == 2) { //RESEARCHER ID 
                    ?>
                        <tr>
                            <td>#1</td>
                            <td><?php echo "<strong>" . $utilizador['researcher_sys1_designacao'] . "</strong>"; ?></td>
                            <?php if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '') {
                                        $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys1_id']; ?>
                                <td><?php echo $utilizador['researcher_sys1_id']; ?> </td>
                                <td><?php echo "<a href='" . $researcherid . "' target='_new'>" . $researcherid . "</a>"; ?></td>
                            <?php } else { ?>
                                <td>Código não definido</td>
                                <td>URL não definido</td>
                            <?php } ?>
                        </tr>
                    <?php } else if ($utilizador['researcher_sys1_type_id'] == 3) { //SCOPUS ID 
                    ?>
                        <tr>
                            <td>#1</td>
                            <td><?php echo "<strong>" . $utilizador['researcher_sys1_designacao'] . "</strong>"; ?></td>
                            <?php if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '') {
                                        $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys1_id']; ?>
                                <td><?php echo $utilizador['researcher_sys1_id']; ?> </td>
                                <td><?php echo "<a href='" . $scopusid . "' target='_new'>" . $scopusid . "</a>"; ?></td>
                            <?php } else { ?>
                                <td>Código não definido</td>
                                <td>URL não definido</td>
                            <?php } ?>
                        <?php } else if ($utilizador['researcher_sys1_type_id'] == 4) { //GOOGLE SCHOLAR 
                        ?>
                        <tr>
                            <td>#1</td>
                            <td><?php echo "<strong>" . $utilizador['researcher_sys1_designacao'] . "</strong>"; ?></td>
                            <?php if (isset($utilizador['researcher_sys1_id']) && $utilizador['researcher_sys1_id'] != '') {
                                        $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys1_id']; ?>
                                <td><?php echo $utilizador['researcher_sys1_id']; ?> </td>
                                <td><?php echo "<a href='" . $google . "' target='_new'>" . $google . "</a>"; ?></td>
                            <?php } else { ?>
                                <td>Código não definido</td>
                                <td>URL não definido</td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } else { // NÃO FOI DEFINIDO
                ?>
                    <tr>
                        <td>#1</td>
                        <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
                    </tr>
                <?php } ?>
                <!-- 2 Sistema -->
                <tr><?php if (isset($utilizador['researcher_sys2_type_id']) && $utilizador['researcher_sys2_type_id'] != '') {
                        if ($utilizador['researcher_sys2_type_id'] == 1) { //ORCID 
                    ?>
                <tr>
                    <td>#2</td>
                    <td><?php echo "<strong>" . $utilizador['researcher_sys2_designacao'] . "</strong>"; ?></td>
                    <?php if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '') {
                                $orcid = "http://orcid.org/" . $utilizador['researcher_sys2_id']; ?>
                        <td><?php echo $utilizador['researcher_sys2_id']; ?> </td>
                        <td><?php echo "<a href='" . $orcid . "' target='_new'>" . $orcid . "</a>"; ?></td>
                    <?php } else { ?>
                        <td>Código não definido</td>
                        <td>URL não definido</td>
                    <?php } ?>
                </tr>
            <?php } else if ($utilizador['researcher_sys2_type_id'] == 2) { //RESEARCHER ID 
            ?>
                <tr>
                    <td>#2</td>
                    <td><?php echo "<strong>" . $utilizador['researcher_sys2_designacao'] . "</strong>"; ?></td>
                    <?php if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '') {
                                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys2_id']; ?>
                        <td><?php echo $utilizador['researcher_sys2_id']; ?> </td>
                        <td><?php echo "<a href='" . $researcherid . "' target='_new'>" . $researcherid . "</a>"; ?></td>
                    <?php } else { ?>
                        <td>Código não definido</td>
                        <td>URL não definido</td>
                    <?php } ?>
                </tr>
            <?php } else if ($utilizador['researcher_sys2_type_id'] == 3) { //SCOPUS ID 
            ?>
                <tr>
                    <td>#2</td>
                    <td><?php echo "<strong>" . $utilizador['researcher_sys2_designacao'] . "</strong>"; ?></td>
                    <?php if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '') {
                                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys2_id']; ?>
                        <td><?php echo $utilizador['researcher_sys2_id']; ?> </td>
                        <td><?php echo "<a href='" . $scopusid . "' target='_new'>" . $scopusid . "</a>"; ?></td>
                    <?php } else { ?>
                        <td>Código não definido</td>
                        <td>URL não definido</td>
                    <?php } ?>
                </tr>
            <?php } else if ($utilizador['researcher_sys2_type_id'] == 4) { //GOOGLE SCHOLAR 
            ?>
                <tr>
                    <td>#2</td>
                    <td><?php echo "<strong>" . $utilizador['researcher_sys2_designacao'] . "</strong>"; ?></td>
                    <?php if (isset($utilizador['researcher_sys2_id']) && $utilizador['researcher_sys2_id'] != '') {
                                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys2_id']; ?>
                        <td><?php echo $utilizador['researcher_sys2_id']; ?> </td>
                        <td><?php echo "<a href='" . $google . "' target='_new'>" . $google . "</a>"; ?></td>
                    <?php } else { ?>
                        <td>Código não definido</td>
                        <td>URL não definido</td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } else { // NÃO FOI DEFINIDO
        ?>
            <tr>
                <td>#2</td>
                <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
            </tr>
        <?php } ?>
        <!-- 3 Sistema -->
        <tr><?php if (isset($utilizador['researcher_sys3_type_id']) && $utilizador['researcher_sys3_type_id'] != '') {
                if ($utilizador['researcher_sys3_type_id'] == 1) { //ORCID 
            ?>
        <tr>
            <td>#3</td>
            <td><?php echo "<strong>" . $utilizador['researcher_sys3_designacao'] . "</strong>"; ?></td>
            <?php if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '') {
                        $orcid = "http://orcid.org/" . $utilizador['researcher_sys3_id']; ?>
                <td><?php echo $utilizador['researcher_sys3_id']; ?> </td>
                <td><?php echo "<a href='" . $orcid . "' target='_new'>" . $orcid . "</a>"; ?></td>
            <?php } else { ?>
                <td>Código não definido</td>
                <td>URL não definido</td>
            <?php } ?>
        </tr>
    <?php } else if ($utilizador['researcher_sys3_type_id'] == 2) { //RESEARCHER ID 
    ?>
        <tr>
            <td>#3</td>
            <td><?php echo "<strong>" . $utilizador['researcher_sys3_designacao'] . "</strong>"; ?></td>
            <?php if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '') {
                        $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys3_id']; ?>
                <td><?php echo $utilizador['researcher_sys3_id']; ?> </td>
                <td><?php echo "<a href='" . $researcherid . "' target='_new'>" . $researcherid . "</a>"; ?></td>
            <?php } else { ?>
                <td>Código não definido</td>
                <td>URL não definido</td>
            <?php } ?>
        </tr>
    <?php } else if ($utilizador['researcher_sys3_type_id'] == 3) { //SCOPUS ID 
    ?>
        <tr>
            <td>#3</td>
            <td><?php echo "<strong>" . $utilizador['researcher_sys3_designacao'] . "</strong>"; ?></td>
            <?php if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '') {
                        $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys3_id']; ?>
                <td><?php echo $utilizador['researcher_sys3_id']; ?> </td>
                <td><?php echo "<a href='" . $scopusid . "' target='_new'>" . $scopusid . "</a>"; ?></td>
            <?php } else { ?>
                <td>Código não definido</td>
                <td>URL não definido</td>
            <?php } ?>
        </tr>
    <?php } else if ($utilizador['researcher_sys3_type_id'] == 4) { //GOOGLE SCHOLAR 
    ?>
        <tr>
            <td>#3</td>
            <td><?php echo "<strong>" . $utilizador['researcher_sys3_designacao'] . "</strong>"; ?></td>
            <?php if (isset($utilizador['researcher_sys3_id']) && $utilizador['researcher_sys3_id'] != '') {
                        $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys3_id']; ?>
                <td><?php echo $utilizador['researcher_sys3_id']; ?> </td>
                <td><?php echo "<a href='" . $google . "' target='_new'>" . $google . "</a>"; ?></td>
            <?php } else { ?>
                <td>Código não definido</td>
                <td>URL não definido</td>
            <?php } ?>
        </tr>
    <?php } ?>
<?php } else { // NÃO FOI DEFINIDO
?>
    <tr>
        <td>#3</td>
        <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
    </tr>
<?php } ?>
<!-- 4 Sistema -->
<tr><?php if (isset($utilizador['researcher_sys4_type_id']) && $utilizador['researcher_sys4_type_id'] != '') {
        if ($utilizador['researcher_sys4_type_id'] == 1) { //ORCID 
    ?>
<tr>
    <td>#4</td>
    <td><?php echo "<strong>" . $utilizador['researcher_sys4_designacao'] . "</strong>"; ?></td>
    <?php if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '') {
                $orcid = "http://orcid.org/" . $utilizador['researcher_sys4_id']; ?>
        <td><?php echo $utilizador['researcher_sys4_id']; ?> </td>
        <td><?php echo "<a href='" . $orcid . "' target='_new'>" . $orcid . "</a>"; ?></td>
    <?php } else { ?>
        <td>Código não definido</td>
        <td>URL não definido</td>
    <?php } ?>
</tr>
<?php } else if ($utilizador['researcher_sys4_type_id'] == 2) { //RESEARCHER ID 
?>
    <tr>
        <td>#4</td>
        <td><?php echo "<strong>" . $utilizador['researcher_sys4_designacao'] . "</strong>"; ?></td>
        <?php if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '') {
                $researcherid = "http://www.researcherid.com/rid/" . $utilizador['researcher_sys4_id']; ?>
            <td><?php echo $utilizador['researcher_sys4_id']; ?> </td>
            <td><?php echo "<a href='" . $researcherid . "' target='_new'>" . $researcherid . "</a>"; ?></td>
        <?php } else { ?>
            <td>Código não definido</td>
            <td>URL não definido</td>
        <?php } ?>
    </tr>
<?php } else if ($utilizador['researcher_sys4_type_id'] == 3) { //SCOPUS ID 
?>
    <tr>
        <td>#4</td>
        <td><?php echo "<strong>" . $utilizador['researcher_sys4_designacao'] . "</strong>"; ?></td>
        <?php if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '') {
                $scopusid = "http://www.scopus.com/authid/detail.url?authorId=" . $utilizador['researcher_sys4_id']; ?>
            <td><?php echo $utilizador['researcher_sys4_id']; ?> </td>
            <td><?php echo "<a href='" . $scopusid . "' target='_new'>" . $scopusid . "</a>"; ?></td>
        <?php } else { ?>
            <td>Código não definido</td>
            <td>URL não definido</td>
        <?php } ?>
    </tr>
<?php } else if ($utilizador['researcher_sys4_type_id'] == 4) { //GOOGLE SCHOLAR 
?>
    <tr>
        <td>#4</td>
        <td><?php echo "<strong>" . $utilizador['researcher_sys4_designacao'] . "</strong>"; ?></td>
        <?php if (isset($utilizador['researcher_sys4_id']) && $utilizador['researcher_sys4_id'] != '') {
                $google = "https://scholar.google.com/citations?user=" . $utilizador['researcher_sys4_id']; ?>
            <td><?php echo $utilizador['researcher_sys4_id']; ?> </td>
            <td><?php echo "<a href='" . $google . "' target='_new'>" . $google . "</a>"; ?></td>
        <?php } else { ?>
            <td>Código não definido</td>
            <td>URL não definido</td>
        <?php } ?>
    </tr>
<?php } ?>
<?php } else { // NÃO FOI DEFINIDO
?>
    <tr>
        <td>#4</td>
        <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
    </tr>
<?php } ?>
                    </table>
                    <hr />
                    <!--PALAVRAS CHAVE E KEYWORDS -->
                    <table class="limpo" style="100%">
                        <tr>
                            <th style="width:150px;"></th>
                            <th style="width:250px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:100px;"></th>
                        </tr>
                        <?php if (isset($utilizador['keywords']) && $utilizador['keywords'] != '') { ?>
                            <tr>
                                <td><strong>Keywords</strong></td>
                                <td colspan="3">
                                    <?php echo $utilizador['keywords']; ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td><strong>Keywords</strong></td>
                                <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
                            </tr>
                        <?php } ?>
                        <?php if (isset($utilizador['palavraschave']) && $utilizador['palavraschave'] != '') { ?>
                            <tr>
                                <td><strong>Palavras Chave</strong></td>
                                <td colspan="3">
                                    <?php echo $utilizador['palavraschave']; ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td><strong>Palavras Chave</strong></td>
                                <td colspan="3"><span style="color:#F00">Solicitar a inserção/atualização deste conteúdo </span> <a href="mailto:falopes@fc.ul.pt">aqui</a></td>
                            </tr>
                        <?php } ?>
                    </table>
                </fieldset>

            </div>
            <div class="tab-pane" id="preview">
                <!-- RENDERIZAÇÃO DA PÁGINA -->

                <!-- GBNT -->

                <div class="page-title">
                    <h1><?php echo utf8_decode($utilizador['nome_a_mostrar']); ?>
                    </h1>
                </div>

                <div class="body group">
                    <div class="content-wrapper pp ru">
                        <div class="pp-id">
                            <div class="pp-id-wrap clearfix">
                                <?php if ($foto_exists) { ?>
                                    <div class="pp-id-picture"><img border="1" height="240" width="240" src="data:image/jpeg;base64, <?php echo base64_encode($utilizador['extra']['foto']['content']) ?>" typeof="foaf:Image" /></div> <?php } ?>
                                <div class="pp-id-info">
                                    <div class="pp-id-contacts span-list">
                                        <h3>Contactos</h3>
                                        <?php if (isset($utilizador['unidade']) && $utilizador['unidade'] != '') { ?>
                                            <span class="dep-uni"><?php echo utf8_decode($utilizador['unidade']); ?></span><br>
                                        <?php } ?>
                                        <?php if (isset($utilizador['contactos_sala']) && $utilizador['contactos_sala'] != '') { ?>
                                            <span>Sala/Gabinete</span> <?php echo $utilizador['contactos_sala']; ?> <br>
                                        <?php } ?>
                                        <?php if (isset($utilizador['contactos_extensao_principal']) && $utilizador['contactos_extensao_principal'] != '') { ?>
                                            <span>Ext. Principal</span> <?php echo $utilizador['contactos_extensao_principal']; ?>
                                        <?php } ?>
                                        <?php if (isset($utilizador['contactos_extensao_alternativa']) && $utilizador['contactos_extensao_alternativa'] != ' ') { ?>
                                            <span>Ext. Alt</span> <?php echo $utilizador['contactos_extensao_alternativa']; ?>
                                        <?php } ?><br>
                                        <?php if (isset($utilizador['contactos_telefone_directo']) && $utilizador['contactos_telefone_directo'] != '') { ?>
                                            <span>Telefone Direto</span> <?php echo $utilizador['contactos_telefone_directo']; ?><br>
                                        <?php } ?>
                                        <?php if (isset($utilizador['mail']) && $utilizador['mail'] != '') { ?>
                                            <span>Email</span>
                                            <?php
                                            if (isset($utilizador['email_publico']) && $utilizador['email_publico'] != NULL) {
                                                $new_mail = str_ireplace(
                                                    '@fc.ul.pt',
                                                    '@ciencias.ulisboa.pt',
                                                    $utilizador['email_publico']
                                                );

                                            ?>
                                                <a href="mailto:<?php echo $new_mail; ?>"><?php echo $new_mail; ?></a><br>
                                            <?php } else {
                                                /* mail de ciencias aon inves de fc.ul.pt */
                                                $new_mail = str_ireplace(
                                                    '@fc.ul.pt',
                                                    '@ciencias.ulisboa.pt',
                                                    $utilizador['mail']
                                                );
                                            ?>
                                                <a href="mailto:<?php echo $new_mail; ?>"><?php echo $new_mail; ?></a><br>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if (isset($utilizador['pagina_pessoal']) && $utilizador['pagina_pessoal'] != '') { ?>
                                            <span><a href="<?php echo $utilizador['pagina_pessoal']; ?>" target="_new">Página Pessoal</a></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="of-content-wrapper of-oferta-formativa">
                            <div class="of-content">
                                <div class="ru-section alt-section">
                                    <div class="of-section-wrap">
                                        <div class="ru-section-content span-list">
                                            <?php if (isset($utilizador['carreira']) && $utilizador['carreira'] != '') { ?>
                                                <span>Carreira</span> <?php echo $utilizador['carreira']; ?><br>
                                            <?php } ?>
                                            <?php if (isset($utilizador['categoria']) && $utilizador['categoria'] != '') { ?>
                                                <span>Categoria</span> <?php echo $utilizador['categoria']; ?><br>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($researcherid != '' || $orcid != '' || $google != '' || $google != '') { ?>
                                    <div class="ru-section alt-section">
                                        <div class="of-section-wrap">
                                            <h3>Indicadores</h3>
                                            <div class="ru-section-content span-list">
                                                <?php if ($researcherid != '') { ?>
                                                    <span><?php echo "<a href='" . $researcherid . "' target='_new'>ResearcherID</a>"; ?></span><br>
                                                <?php } ?>
                                                <?php if ($orcid != '') { ?>
                                                    <span><?php echo "<a href='" . $orcid . "' target='_new'>Orcid</a>"; ?></span><br>
                                                <?php } ?>
                                                <?php if ($scopusid != '') { ?>
                                                    <span><?php echo "<a href='" . $scopusid . "' target='_new'>Scopus</a>"; ?></span><br>
                                                <?php } ?>
                                                <?php if ($google != '') { ?>
                                                    <span><?php echo "<a href='" . $google . "' target='_new'>Google Scholar</a>"; ?></span><br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ((isset($utilizador['palavraschave']) && $utilizador['palavraschave'] != '') || (isset($utilizador['keywords']) && $utilizador['keywords'] != '')) { ?>
                                    <div class="ru-section alt-section">
                                        <div class="of-section-wrap">
                                            <?php if (isset($utilizador['palavraschave']) && $utilizador['palavraschave'] != '') {
                                                $Palavras = explode(';', $utilizador['palavraschave']); ?>
                                                <h3>Palavras Chave</h3>
                                                <div class="ru-section-content section-list">
                                                    <ul class="section-content-keywords">
                                                        <?php for ($i = 0; $i < count($Palavras); $i++) {
                                                            echo "<li>" . $Palavras[$i] . "</li>";
                                                        } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <?php if (isset($utilizador['keywords']) && $utilizador['keywords'] != '') {
                                                $Keywords = explode(';', $utilizador['keywords']); ?>
                                                <h3>Keywords</h3>
                                                <div class="ru-section-content section-list">
                                                    <ul class="section-content-keywords">
                                                        <?php for ($i = 0; $i < count($Keywords); $i++) {
                                                            echo "<li>" . $Keywords[$i] . "</li>";
                                                        } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="ru-section">
                                    <div class="of-section-wrap">
                                        <?php if (isset($utilizador['cv_resumido']) && $utilizador['cv_resumido'] != '') { ?>
                                            <h2>Currículo Resumido</h2>
                                            <div class="ru-section-content">
                                                <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['cv_resumido']);
                                                foreach ($arrayParagrafos as $a) {
                                                    echo "<p>" . $a . "</p>";
                                                } ?>
                                                <p><?php //echo $utilizador['cv_resumido']; 
                                                    ?></p>
                                            <?php } ?>
                                            <?php if ($cv_exists) { ?>
                                                <!--<a class="doc-download btnCI" >Download currículo completo <i class="fa fa-arrow-down"></i></a>-->
                                                <?php echo "<a class='doc-download btnCI' href='/servicos/perfil/PaginaPessoal/showFile/?id=" . $utilizador['extra']['cv']['id'] . "&tipo=cv&user=" . $utilizador['mail'] . "'>Download currículo completo <i class='fa fa-arrow-down'></i></a>"; ?>
                                            <?php } ?>
                                            </div>
                                    </div>
                                </div>
                                <?php if (isset($utilizador['interesses_cientificos']) && $utilizador['interesses_cientificos'] != '') { ?>
                                    <div class="ru-section">
                                        <div class="of-section-wrap">
                                            <h2>Interesses Científicos</h2>
                                            <div class="ru-section-content">
                                                <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['interesses_cientificos']);
                                                foreach ($arrayParagrafos as $a) {
                                                    echo "<p>" . $a . "</p>";
                                                } ?>
                                                <p><?php // echo $utilizador['interesses_cientificos']; 
                                                    ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (isset($utilizador['scientific_interests']) && $utilizador['scientific_interests'] != '') { ?>
                                    <div class="ru-section">
                                        <div class="of-section-wrap">
                                            <h2>Scientific Interests</h2>
                                            <div class="ru-section-content">
                                                <?php $arrayParagrafos = explode("&lt;br&gt;",  $utilizador['scientific_interests']);
                                                foreach ($arrayParagrafos as $a) {
                                                    echo "<p>" . $a . "</p>";
                                                } ?>
                                                <p><?php // echo $utilizador['scientific_interests']; 
                                                    ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ((isset($utilizador['pub1']) && $utilizador['pub1'] != '') ||
                                    (isset($utilizador['pub2']) && $utilizador['pub2'] != '') ||
                                    (isset($utilizador['pub3']) && $utilizador['pub3'] != '') ||
                                    (isset($utilizador['pub4']) && $utilizador['pub4'] != '') ||
                                    (isset($utilizador['pub5']) && $utilizador['pub5'] != '')
                                ) { ?>
                                    <div class="ru-section">
                                        <div class="of-section-wrap">
                                            <h2>Publicações selecionadas</h2>
                                            <div class="ru-section-content pp-selected-publications">
                                                <ul>
                                                    <?php if (isset($utilizador['pub1']) && $utilizador['pub1'] != '') { ?>
                                                        <li><?php echo $utilizador['pub1']; ?></li>
                                                    <?php } ?>
                                                    <?php if (isset($utilizador['pub2']) && $utilizador['pub2'] != '') { ?>
                                                        <li><?php echo $utilizador['pub2']; ?></li>
                                                    <?php } ?>
                                                    <?php if (isset($utilizador['pub3']) && $utilizador['pub3'] != '') { ?>
                                                        <li><?php echo $utilizador['pub3']; ?></li>
                                                    <?php } ?>
                                                    <?php if (isset($utilizador['pub4']) && $utilizador['pub4'] != '') { ?>
                                                        <li><?php echo $utilizador['pub4']; ?></li>
                                                    <?php } ?>
                                                    <?php if (isset($utilizador['pub5']) && $utilizador['pub5'] != '') { ?>
                                                        <li><?php echo $utilizador['pub5']; ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <p><a href="https://biblios.ciencias.ulisboa.pt/autor/<?php echo $utilizador['username']; ?>" class="btnCI btnCI-default" target="_blank">Ver todas as Publicações</a></p>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div><!-- .of-content -->
                        </div><!-- .of-content-wrapper -->
                    </div><!-- .content-wrapper -->
                </div><!-- .body -->
            </div><!-- .content-area -->
        </div>
        <!-- .central —>

<!-- GBNT -->
    </div>
    </div>
    </div>
    <!-- Fecha a query que separa a visita/consulta da edição de dados -->

<?php } ?>
<p style="clear:both"></p>


<script>
    function req(method, url, callback) {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
                if (xmlhttp.status == 200) {
                    return callback(undefined, xmlhttp.responseText)
                } else {
                    return callback({
                        status: xmlhttp.readyState,
                        response: xmlhttp.responseText
                    })
                }
            }
        };

        xmlhttp.open(method.toUpperCase(), url, true);
        xmlhttp.send();
    }
    console.log('result');

    req('get', '/servicos/pedidosVoip/extensoes/search?utilizador=<?= $utilizador['username'] ?>', (err, data) => {
        data = JSON.parse(data)

        if (data.length == 1) {
            data = data[0]
            const extPessoal = document.getElementById('extPessoal')
            const extPessoalDiv = document.getElementById('divExtPessoalVoip')
            extPessoal.innerHTML = data.id
            extPessoalDiv.style.display = 'block'
        }
    })
</script>
