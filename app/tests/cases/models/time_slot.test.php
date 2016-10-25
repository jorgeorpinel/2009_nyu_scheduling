<?php 
/* SVN FILE: $Id$ */
/* TimeSlot Test cases generated on: 2009-09-09 11:52:43 : 1252511563*/
App::import('Model', 'TimeSlot');

class TimeSlotTestCase extends CakeTestCase {
	var $TimeSlot = null;
	var $fixtures = array('app.time_slot', 'app.faculty', 'app.lesson');

	function startTest() {
		$this->TimeSlot =& ClassRegistry::init('TimeSlot');
	}

	function testTimeSlotInstance() {
		$this->assertTrue(is_a($this->TimeSlot, 'TimeSlot'));
	}

	function testTimeSlotFind() {
		$this->TimeSlot->recursive = -1;
		$results = $this->TimeSlot->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('TimeSlot' => array(
			'id'  => 1,
			'faculty_id'  => 1,
			'days'  => 'Lorem',
			'start_time'  => '11:52:43',
			'end_time'  => '11:52:43'
		));
		$this->assertEqual($results, $expected);
	}
}
?>