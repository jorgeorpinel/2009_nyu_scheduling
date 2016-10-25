<?php 
/* SVN FILE: $Id$ */
/* Location Test cases generated on: 2009-09-08 16:43:00 : 1252442580*/
App::import('Model', 'Location');

class LocationTestCase extends CakeTestCase {
	var $Location = null;
	var $fixtures = array('app.location', 'app.lesson');

	function startTest() {
		$this->Location =& ClassRegistry::init('Location');
	}

	function testLocationInstance() {
		$this->assertTrue(is_a($this->Location, 'Location'));
	}

	function testLocationFind() {
		$this->Location->recursive = -1;
		$results = $this->Location->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Location' => array(
			'id'  => 1,
			'building'  => 'Lorem ipsum dolor sit amet',
			'room'  => 'Lorem ipsum dolor sit amet'
		));
		$this->assertEqual($results, $expected);
	}
}
?>