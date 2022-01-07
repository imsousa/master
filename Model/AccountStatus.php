<?php

class AccountStatus extends AppModel{
    public $name = 'AccountStatus';
    public $useTable = 't_acc_status';
	public $primaryKey = 'id';
	public $displayField = 'label';
	public $useDbConfig = 'dbgestao';
}
