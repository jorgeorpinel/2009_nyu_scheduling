<?php 
/* SVN FILE: $Id$ */
/* Period Fixture generated on: 2009-09-08 16:48:16 : 1252442896*/

class PeriodFixture extends CakeTestFixture {
	var $name = 'Period';
	var $table = 'periods';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'year' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'year'  => 1
	));
}
?>