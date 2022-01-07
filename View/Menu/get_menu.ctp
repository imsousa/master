
 <div style="background-color: #2c3fb1; color:white;padding:5px;">
	<div  style="margin-bottom:10px;margin-left:15px;margin-top:10px;">
       <?php if( isset($data_foto) ){ ?>
            <!--<img border="0" height="13" width="13"  src="data:image/jpeg;base64, <?php echo base64_encode($data_foto)?>" typeof="foaf:Image"/>-->
       <?php } ?>
        <strong><?php echo strtoupper($username); ?></strong> 
<div id="login_notifs" style="color:white ;margin-top:5px;"></div>
         
     </div>
 </div>
            

<nav role="navigation" style="margin-bottom:0x;">
    <ul class="nav" role="navigation">
    	<?php parseTree($menu); ?>
    </ul>
</div>

<style type="text/css">
.nohref {
	box-sizing: border-box;
    color: rgba(44, 63, 177, 1);
    display: inline-block;
    font-size: 20px;
    line-height: 28px;
    padding: 7px 10px;
    text-decoration: none;
    width: 100%;
	cursor:pointer;
	display: block;
	
   
}
</style>

<?php 
function parseTree ($nodes) {
	foreach ($nodes as $node) {
		$icon = ($node["MenuItem"]['icon']!=NULL) ? '<i class="fa '.$node["MenuItem"]['icon'].'" style="min-width:15px;font-size:13px;"></i>' : '<i class="fa" style="min-width:15px;">&nbsp;›</i>';
		//pr($node);
		if (isset($node["children"]) && count($node["children"]) > 0){
			// echo start ul
			$new_icon = NULL;
			if($node["MenuItem"]["id"]==214) {
				$new_icon =  '<img src="/servicosCake/perfil/img/novo-icon.png">' ;
			}
			echo '
			 <li class="first  collapsed">
			 
			    <span class="nohref" style="font-size:16px;">'.$icon.' '.($node["MenuItem"]["label"]).' '.$new_icon.'</span>
			    <a class="setinha" href="#"></a>
            <ul style="display: none;" class="submenu">
					
					
			';
			
			// submenu
			parseTree($node["children"]);
			
			// echo end ul
			echo '
				</ul>
			</li>	
			';
		} else {
			if ($node["MenuItem"]["other_window"]){ 
				$target = 'target="_blank"';
			} else {
				$target = '';
			}
			
			if ($node["MenuItem"]["full_url"]){
				$url = $node["MenuItem"]["url"];
				$class = "";
			} 
			else {
				$url = '#page='.$node["MenuItem"]["url"];
				$class = "ajax"; 
			}
			
		
			
			if ($node["MenuItem"]["plano_b"]){ 
				$url = '#page=/servicos/fcul/Main/explicacao/'.str_replace("/", "|", $node["MenuItem"]["url"]);
					
			}
		
			echo '<li class="leaf"><a  style="font-size:16px;" class="'.$class.'" href="'.$url.'" '.$target.'>'.$icon.' '.($node["MenuItem"]["label"]).'</a></li>';
		}	
	}
}
?>


<script src="/sites/all/themes/cienciasgbnt/js/ciencias.js?nuivx7"></script>
<script src="/servicos/common/js/dialogas.js"></script>
<script type="text/javascript">

function getBlocoNotificacoes() {
	$(".login").children().children().first().append(' <span id="small_notif" stylE=""></span>');
	
    $.post('/servicos/perfil/BlocoNotificacoes', {}, function(data){
       $("#login_notifs").html(data);  
    });
}
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
	/*
	smoothScroll
	*/
	 $('html,body').animate({
		         scrollTop: $(".page").offset().top},     
				    'slow');
	
	
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





$(document).ready(function() {
	
	getBlocoNotificacoes();
	
	hashChanger();
	
	if(window.location.hash == '')
		window.location.hash = "page=/servicos/perfil/Conta/";
	
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
			 //$("html, body").animate({ scrollTop: 0 }, "slow");  -- COMMENT
			 
		});
	}
}



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
		class: "ciencias content",
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
	$(".ui-dialog").addClass("content")
		$(".ui-dialog").addClass("ciencias");
});


/*MENU*/
$(".nohref").click(function() {
	
	var li = $(this).parent('li');
	var submenu = li.children('ul.submenu');
	//collapsed
	//expanded
	if(submenu.is(":visible")) {
		submenu.hide('fast');
		li.removeClass("expanded");
		li.addClass("collapsed");
	}else{
		submenu.show('fast');
		li.removeClass("collapsed");
		li.addClass("expanded");
	}
});

</script>