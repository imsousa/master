
 <div style="background-color: #2c3fb1; color:white;padding:5px;">
	<div  style="margin-bottom:10px;margin-left:15px;margin-top:10px;">
       <?php if( isset($data_foto) ){ ?>
            <!--<img border="0" height="13" width="13"  src="data:image/jpeg;base64, <?php echo base64_encode($data_foto)?>" typeof="foaf:Image"/>-->
       <?php } ?>
        <strong><?php echo strtoupper($username); ?></strong> 
         <br>
     </div>
 </div>
            

<nav role="navigation" style="margin-bottom:0x;">
    <ul class="nav" role="navigation">
    	<?php parseTree($menu); ?>
    </ul>
</div>

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
			 
			    <a class="nolink" href="#">'.$icon.' '.($node["MenuItem"]["label"]).' '.$new_icon.'</a>
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
			} else {
				$url = '#page='.$node["MenuItem"]["url"];
				$class = "ajax"; 
			}
			
		
			echo '<li><a class="'.$class.'" href="'.$url.'" '.$target.'>'.$icon.' '.($node["MenuItem"]["label"]).'</a></li>';
		}	
	}
}
?>

<script type="text/javascript">


/** beginning of file **/
(function ($) {
    setTimeout(function() {
          $(".logo").css("display", "block");
    }, 100);

    function logo(){
        $logoHeight = 305;
        $scrollolo = $(window).scrollTop();
        $combinedHeight = $logoHeight - $scrollolo;
        $min = 60; // minimum drag value
        $pos = 150; // slider position
        $opac = (($combinedHeight-$min) / ($logoHeight-$min))+0; // 37.5%

        $(".logo-wrapper").height(function(){
            $(this).height($combinedHeight);
            $(".logo").css({"opacity": $opac});
            if ($combinedHeight < 61){
                $(this).height(60).addClass("fixed");

            } else {
                $(this).removeClass("fixed");
            }
        });
    }

$(document).ready(function () {
    if($("body").hasClass("not-front")) {
    window.scroll(0,246);
    }
    // var centralH = $("#central").height();
    // $(".main-wrapper").css("min-height",centralH-306);

    $(".quicklinks li:nth-child(4), .quicklinks li:nth-child(7)").addClass("clear");

    if($("body").hasClass("toolbar")) {
        
        $(".breadcrumb-wrapper").scrollToFixed({ 
        marginTop: 125,
        fixed: function() { $(this).addClass("breadfixed"); },
        postFixed: function() { $(this).removeClass("breadfixed"); }
        });
        var tocOffsetValue = -164;
    } else {
        $(".breadcrumb-wrapper").scrollToFixed({ 
        marginTop: 60,
        fixed: function() { $(this).addClass("breadfixed"); },
        postFixed: function() { $(this).removeClass("breadfixed"); }
        });
        var tocOffsetValue = -99;

    }
    $("p:empty").remove()


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
		 $.scrollTo( $(this));
        /*if($("body").hasClass("toolbar")) {
            $.scrollTo( '#main-content', 300, { offset:-90}, {easing: 'easeOutQuart'} );
        } else {
            $.scrollTo( '#main-content', 300, { offset:-60}, {easing: 'easeOutQuart'} );
        }*/
    });

    
    
    $(".backtotop").localScroll({
                queue:true,
                duration:800,
                hash:true,
                easing:'easeOutQuart',
                onBefore:function( e, anchor, $target ){
                    // The 'this' is the settings object, can be modified
                },
                onAfter:function( anchor, settings ){
                    // The 'this' contains the scrolled element (#content)
                }
            });

$('#toc').toc({
    'selectors': 'h2:not(".no-anchor")', //elements to use as headings
    'container': '#main-content .content', //element to find all selectors in
    'smoothScrolling': false, //enable or disable smooth scrolling on click
    'prefix': 'toc', //prefix for anchor tags and class names
    'onHighlight': function(el) {}, //called when a new section is highlighted 
    'highlightOnScroll': true, //add class to heading that is currently in focus
    'highlightOffset': 100, //offset to trigger the next headline
    'anchorName': function(i, heading, prefix) { //custom function for anchor name
        return prefix+i;
    },
    'headerText': function(i, heading, $heading) { //custom function building the header-item text
        return $heading.text();
    },
    'itemClass': function(i, heading, $heading, prefix) { // custom function for item class
      return $heading[0].tagName.toLowerCase();
    }
    }).localScroll({
        queue:true,
        duration:800,
        offset: tocOffsetValue,
        hash:true,
        easing:'easeOutQuart',
        onBefore:function( e, anchor, $target ){
            // The 'this' is the settings object, can be modified
        },
        onAfter:function( anchor, settings ){
            // The 'this' contains the scrolled element (#content)
        }
});


if ($('#toc ul li').length) { // implies *not* zero
    $("#toc").prepend("<h3>Nesta PÃ¡gina:</h3>");
}

$('.destaque-gallery').flickity({
  // options
  imagesLoaded: true,
  cellAlign: 'left',
  prevNextButtons: false,
  contain: true
});


});

$(window).scroll(function() {
logo();
});

$(window).resize(function() {
logo();
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