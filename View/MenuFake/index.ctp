


 <div style="background-color: #2c3fb1; color:white;padding:5px;">
			<div  style="margin-bottom:10px;margin-left:15px;margin-top:10px;">
            
            <?php if( isset($data_foto) ){ ?>
            <!--<img border="0" height="13" width="13"  src="data:image/jpeg;base64, <?php echo base64_encode($data_foto)?>" typeof="foaf:Image"/>-->
            <?php } ?>
            
			<strong><?php echo strtoupper($username); ?></strong> 
            <br>
            </div>
            </div>
            
            
<nav role="navigation">
    <ul class="nav" role="navigation">
        <li class="first  collapsed"><a class="nolink" href="#"> A minha conta</a> <a class="setinha" href="#"></a>
            <ul style="display: none;" class="submenu">
                 <li class="first  leaf">
                    <a href="/servicos/perfil/Conta/" class="ajax">Detalhes</a>
                 </li>
                  <li class=" leaf">
                    <a href="/servicos/perfil/CorreioEletronico/" class="ajax">Correio Eletrónico</a>
                 </li>
                 <li class=" leaf">
                    <a href="/servicos/perfil/Conta/mudarPassword" class="ajax">Mudar Palavra-Passe</a>
                 </li>
                <li class=" leaf">
                   <a href="/servicos/perfil/Conta/mecanismosRecuperacao" class="ajax">Mecanismos de Recuperação</a>
                 </li>
                  <li class="last leaf">
                    <a href="/servicos/perfil/ListasDistribuicao/" class="ajax">Subscrições</a>
                 </li>  
            </ul>
        </li>
         <li class="menu-mlid-340 collapsed">
            <a href="/servicos/perfil/SistemaImpressao/impressoes" class="ajax">Sist. Impressão</a>
        </li>
        
        
        <li class="menu-mlid-340 collapsed">
            <a href="/servicos/perfil/ControloAcesso/" class="ajax">Controlo de Acesso</a>
        </li>
        
        <li class="menu-mlid-340 collapsed">
            <a href="/servicos/diretorio/Main/" class="ajax">Diretorio</a>
        </li>
        
         <li class="menu-mlid-340 collapsed">
            <a href="/servicos/perfil/Menu/editMenu" class="ajax">Gestão Menu</a>
        </li>
        
    </ul>
</nav>




<script type="text/javascript">


(function ($) {
  

$(document).ready(function () {
	
	
    if($("body").hasClass("not-front")) {
    window.scroll(0,246);
    }
    // var centralH = $("#central").height();
    // $(".main-wrapper").css("min-height",centralH-306);

   

    $("li.expanded").toggleClass("expanded collapsed");
    $("li.active-trail.collapsed").toggleClass("collapsed expanded");

    function open(){
        $(".expanded .submenu").stop().slideDown(300);
        $(".collapsed .submenu").stop().slideUp(300);
    }

    $(".collapsed .submenu").hide();

    $("a.setinha").click( function(e){
        e.preventDefault();
        if ($(this).parent().hasClass("expanded")) {
                $(this).parent().find(".expanded").toggleClass("expanded collapsed");
                $(this).parent().toggleClass("expanded collapsed");
        } else {
            $(this).parents("li").siblings(".expanded").find(".expanded").toggleClass("expanded collapsed");
            $(this).parents("li").siblings(".expanded").toggleClass("expanded collapsed");
            $(this).parent().toggleClass("collapsed expanded");
        }
        open();
        if($("body").hasClass("logged-in")) {
            $.scrollTo( '#main-content', 300, { offset:-125}, {easing: 'easeOutQuart'} );
        } else {
            $.scrollTo( '#main-content', 300, { offset:-60}, {easing: 'easeOutQuart'} );
        }
    });
	


	/* FIX PARA MENU */
	$("a.nolink").click( function(e){
        e.preventDefault();
        if ($(this).parent().hasClass("expanded")) {
                $(this).parent().find(".expanded").toggleClass("expanded collapsed");
                $(this).parent().toggleClass("expanded collapsed");
        } else {
            $(this).parents("li").siblings(".expanded").find(".expanded").toggleClass("expanded collapsed");
            $(this).parents("li").siblings(".expanded").toggleClass("expanded collapsed");
            $(this).parent().toggleClass("collapsed expanded");
        }
        open();
        if($("body").hasClass("logged-in")) {
            $.scrollTo( '#main-content', 300, { offset:-125}, {easing: 'easeOutQuart'} );
        } else {
            $.scrollTo( '#main-content', 300, { offset:-60}, {easing: 'easeOutQuart'} );
        }
    });

});
})(jQuery);


</script>


<script type="text/javascript">

function getFoto() {
   $(".logo").html('<img src="/servicos/common/img/ajax-loader.gif">');
   $.post('/servicos/perfil/Conta/getFoto', {}, function(data){
       $(".logo").html(data);  
   });
}

function reloadPage(url){
   $("#main_perfil_div").html('<img src="/servicos/common/img/ajax-loader.gif">');
   $.post(url, {}, function(data){
       $("#main_perfil_div").html(data);  
   });
}

function _HASH(tag){
                var loc;
                var aux;
                var current = window.location.hash;
                if(current){
                               current = current.substring(1).split('&');
                               for(var i = 0; i < current.length; i++){
                                               aux = current[i].split('=');
                                               if (aux[0] == tag){
                                                               loc = aux[1];
                                                               break;
                                               } 
                               }
                }  
                return loc;
}


/**
 * HASH CHANGER
 */
 
 
 
 
$(document).off("click", ".ajax");
$(document).on("click", ".ajax", function(event){
	 event.preventDefault();
	 var href = $(this).attr('href');
	 var parts = href.split("#");
	 if (parts.length > 1) {
		if (parts[1].substr(0,4) == "page"){
			parts = parts[1].split("=");
			href = parts[1];
		}			 	
	 }
	 
	 if(href==_HASH('page'))
	 	hashChanger();
	 else
		 window.location.hash = "page=" + href;

	 /*$(".menu_perfil").find("a").attr('style', '');
	 if($(this).parent().hasClass('nosubmenu')){
		$(this).attr('style', 'background-color: #11C7D2;   background-image: url("/sites/all/themes/fcul/img/menu_lateral_btn_hover.png");    background-repeat: repeat-x;');
	 }else{
		 $(this).attr("style", "color: #0495AD;"); 
	 }*/
});



$(document).off("click", ".dialog");
$(document).on("click", ".dialog", function (e){
	e.preventDefault();
	var href = $(this).attr("href");
	//alert(href);
	var dialog = document.createElement("div");  
	
	var w = '600';
	if ($(this).hasClass("bigDialog"))
		w = "980";
	
	if ($(this).data("width"))
		w = $(this).data("width");
	
	$(dialog).html("<img src='/servicos/common/img/ajax-loader.gif'/>");
	
	$(dialog).load(href);      
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




$(document).ready(function() {
	if(window.location.hash == '')
		window.location.hash = "page=/servicos/perfil/Conta/";
	
	hashChanger();
	
	//getFoto();
});
 
 
$(window).off('hashchange')
$(window).on('hashchange', function() {
	hashChanger();
});
function hashChanger(){
	
	if(_HASH('page') && !_HASH('overlay')){
		$("#main_perfil_div").html('<img src="/servicos/common/img/ajax-loader.gif">');
		$.get(_HASH('page'), function(data){
			$("#main_perfil_div").html(data);
			 $("html, body").animate({ scrollTop: 0 }, "slow"); 
			 
		});
	}
}






function infoMsg(msg) {
	var dialog = document.createElement("div");
	dialog.setAttribute("id","dialog")
	$(dialog).dialog({
		title: 'Info',
		autoOpen: false,
		width: 400,
		height: 130,
		modal: false,
		autoResize: 'auto',
		resizable: false,
		open: function(){
				$(dialog).html('<div style="margin-top:15px;">' + msg + '</div>');
		},
		close: function(){
			$(dialog).html('');
			$( dialog ).dialog( "destroy" ).remove();
		}
	});
	$(dialog).dialog('open');
}


function dialogas(u,t,w,h){
	var html;
	$.get(u, function(data){
		html=data;
		var dialog = document.createElement("div");
		dialog.setAttribute("id","dialog")
		titulo=t;
		$(dialog).dialog({
			title: titulo,
			autoOpen: false,
			width: w,
			height: h,
			modal: true,
			autoResize: 'auto',
			resizable: false,
			buttons: {
             "Fechar": function() {
             	 $( dialog ).dialog( "close" );
               },
             }, 
			open: function(){
				$(dialog).html(html);
			},
			close: function(){
				$(dialog).html('');
				$( dialog ).dialog( "destroy" ).remove();
			}
		});
		$(dialog).dialog('open');
	});
} 
</script>

