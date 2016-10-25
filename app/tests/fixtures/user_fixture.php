<?php 
/* SVN FILE: $Id$ */
/* User Fixture generated on: 2009-09-09 11:55:19 : 1252511719*/

class UserFixture extends CakeTestFixture {
	var $name = 'User';
	var $table = 'users';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'nyu_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 9),
		'first_name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'last_name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'email' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'nyu_id'  => 'Lorem i',
		'first_name'  => 'Lorem ipsum dolor sit amet',
		'last_name'  => 'Lorem ipsum dolor sit amet',
		'email'  => 'Lorem ipsum dolor sit amet',
		'password'  => 'Lorem ipsum dolor sit amet'
	));
}
?>