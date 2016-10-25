<?php 
/* SVN FILE: $Id$ */
/* Course Test cases generated on: 2009-09-08 15:05:36 : 1252436736*/
App::import('Model', 'Course');

class CourseTestCase extends CakeTestCase {
	var $Course = null;
	var $fixtures = array('app.course', 'app.period', 'app.lesson');

	function startTest() {
		$this->Course =& ClassRegistry::init('Course');
	}

	function testCourseInstance() {
		$this->assertTrue(is_a($this->Course, 'Course'));
	}

	function testCourseFind() {
		$this->Course->recursive = -1;
		$results = $this->Course->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Course' => array(
			'id'  => 1,
			'period_id'  => 1,
			'full_number'  => 'Lorem ipsu',
			'title'  => 'Lorem ipsum dolor sit amet',
			'credits'  => 1
		));
		$this->assertEqual($results, $expected);
	}
}
?>