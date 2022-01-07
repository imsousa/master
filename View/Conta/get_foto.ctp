


    	<a href="/user"> 
			<?php if( isset($data_foto) ){ ?>
            <img border="0" height="200" width="200"  src="data:image/jpeg;base64, <?php echo base64_encode($data_foto)?>" typeof="foaf:Image"/>
            <div style="background-color: #2c3fb1; color:white;padding:5px;">
			<div  style="margin-bottom:10px;margin-left:15px;margin-top:10px;">
			<strong><?php echo strtoupper($username); ?></strong>
            <br>
            <div style="padding-top:10px;">
            <i class="fa fa-envelope"></i> 0  Emails<br>
             <i class="fa fa-envelope"></i> 0  Msg. Moodle
             </div>
   			 </div>
             
    
		</div>
            <?php } else { ?>
            <img border="0" height="200" width="200" src="" typeof="foaf:Image">
            <?php } ?>
    	</a>
        
        
        
        
       

