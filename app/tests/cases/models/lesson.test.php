<?php 
/* SVN FILE: $Id$ */
/* Lesson Test cases generated on: 2009-09-08 16:36:20 : 1252442180*/
App::import('Model', 'Lesson');

class LessonTestCase extends CakeTestCase {
	var $Lesson = null;
	var $fixtures = array('app.lesson', 'app.course', 'app.time_slot', 'app.location', 'app.user');

	function startTest() {
		$this->Lesson =& ClassRegistry::init('Lesson');
	}

	function testLessonInstance() {
		$this->assertTrue(is_a($this->Lesson, 'Lesson'));
	}

	function testLessonFind() {
		$this->Lesson->recursive = -1;
		$results = $this->Lesson->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Lesson' => array(
			'id'  => 1,
			'course_id'  => 1,
			'time_slot_id'  => 1,
			'student_id'  => 1,
			'location_id'  => 1
		));
		$this->assertEqual($results, $expected);
	}
}
?>