<b>Criar um item de menu</b>
<ul>
    <li>Clickar no botão [ <i class="icon-plus-sign"></i> Novo Menu Item ] na barra de topo, prencher o formulario e gravar.</li>
    <li>Alternativamente, pode clicar no botão <i class="icon-plus-sign"></i> de um item pai, isto vai pre-preencher o campo "Parent".</li>
    <li>
    	Opções
        <ul style="list-style:circle;">
        	<li><b>Full URL:</b> Utiliza o URL completo, isto permite criar links externos, links com HTTPS, mailtos, javascript, o conteudo do campo URL 
            	vai ser copiado inteiro dentro da tag HREF.</li>
            <li><b>Abre Janela:</b> Abre o link numa nova janela.</li>
            <li><b>Publico:</b> Aparece no menu de qualquer pessoa, ignorando a ACL.</li>
        </ul>
    </li>
</ul>

<hr/>

<b>ACL do menu</b>
<ul>
	<li>Items publicos aparecem no menu de todos os utilizadores.</li>
    <li>Os grupos DOCENTE, FUNCIONARIO e ALUNO não existem no directorio mas existem no sistema de menu.</li>
    <li>Um item não público só aparece no menu do utilizador se o utilizador ou algum dos seus grupos esteja na ACL desse item.</li>
    <li>Um item "child" não aparece a menos que tanto o "child" como o "parent" tenham o utilizador ou algum dos seus grupos nas suas ACL.</li>
</ul>

<hr/>

<b>Interacção com o menu</b>
<ul>
    <li>Clicar na linha de um item "parent" apresenta ou esconde os seus filhos.</li>
    <li>Arrastar uma linha reordena o menu, pode tambem usar os botões <i class="icon-arrow-up"></i> e <i class="icon-arrow-downn"></i>.</li>
    <li>Alterações no menu tem de ser gravadas no botão azul [ <i class="icon-ok"></i> Gravar ] no topo da lista.</li>
</ul>

<hr/>

<b>Apagar um item de menu</b>
<ul>
    <li>Clickar no botão <i class="icon-remove"></i> apaga o menu item.</li>
</ul>