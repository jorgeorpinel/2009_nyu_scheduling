<?php 
/* SVN FILE: $Id$ */
/* Location Fixture generated on: 2009-09-08 16:43:00 : 1252442580*/

class LocationFixture extends CakeTestFixture {
	var $name = 'Location';
	var $table = 'locations';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'building' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'room' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'building'  => 'Lorem ipsum dolor sit amet',
		'room'  => 'Lorem ipsum dolor sit amet'
	));
}
?>