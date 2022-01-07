<?php
class UserLog extends AppModel{
    public $name = 'UserLog';
    public $useTable = 't_userslog';
	public $primaryKey = 'id';
	public $displayField = 'action';
	public $useDbConfig = 'dbgestao';
}
