<?php

if($estudante) {
	echo $this->element('index_aluno');
}else{
	echo $this->element('index_func');
}

?>