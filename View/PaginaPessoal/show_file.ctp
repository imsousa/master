<?php
    if($size !=NULL && $type !=NULL && $name !=NULL){
	header("Content-length: ".$size);
	header("Content-type: ".$type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	echo $content;
	exit;
	}else{
		die('Erro ao obter ficheiro');
	}
?>