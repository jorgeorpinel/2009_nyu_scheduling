<?php 
/* SVN FILE: $Id$ */
/* Period Test cases generated on: 2009-09-08 16:48:16 : 1252442896*/
App::import('Model', 'Period');

class PeriodTestCase extends CakeTestCase {
	var $Period = null;
	var $fixtures = array('app.period', 'app.course');

	function startTest() {
		$this->Period =& ClassRegistry::init('Period');
	}

	function testPeriodInstance() {
		$this->assertTrue(is_a($this->Period, 'Period'));
	}

	function testPeriodFind() {
		$this->Period->recursive = -1;
		$results = $this->Period->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Period' => array(
			'id'  => 1,
			'year'  => 1
		));
		$this->assertEqual($results, $expected);
	}
}
?>