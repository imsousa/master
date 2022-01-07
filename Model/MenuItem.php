<?php
class MenuItem extends AppModel {
        public $useDbConfig = 'dbmenu';
        public $name = 'MenuItem';
		public $useTable = "menu_item";
		
		public $validate = array(
			'label' => array(
				'rule'       => 'notEmpty', 
				'required'   => true,
				'message'    => 'A Label é obrigatória.'
			)
		);
}
     