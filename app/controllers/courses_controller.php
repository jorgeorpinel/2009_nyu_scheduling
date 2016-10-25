<?php
// xxx: use CakePHP vendor import:
require_once '../../vendors/excel/reader.php';

/**
 * @copyright (c) New York University 2009
 * @author Jorge Orpinel
 */
class CoursesController extends AppController {

	var $name = 'Courses';
	var $helpers = array('Html', 'Form');
	
	
	/** Lists all the courses for a given user
	 * View at /my_courses /courses or /courses/index
	 * Redirects to /admin/courses if user is admin
	 * @param $courseId full course number of a specific course to isolate in list
	 */
  function index($courseId = null) { if(!$courseId and isset($this->params['id'])) $courseId = $this->params['id'];
    $this->pageTitle = 'My Courses';
    
    // if user is admin redirect to /admin/courses:
    $userType = $this->Session->read('User.type'); $userTypeC = ucwords($userType);
    if($this->Session->read('User.type') == 'admin') $this->redirect('/admin/courses');
    
    // get the student's courses:
    $userType = $this->Session->read('User.type');
    $userId = $this->Session->read('User.id');
    $periodId = $this->Session->read('Period.id');
    
    if($userType == 'student') {
      $this->Course->Period->unbindModel(array( 'hasAndBelongsToMany'=>array('Course') ));
      $this->Course->Period->bindModel(array( 'hasAndBelongsToMany'=>array('Student'=>array(
        'className' => 'User',
        'joinTable' => 'periods_students',
        'foreignKey' => 'period_id',
        'associationForeignKey' => 'student_id',
        'conditions' => "Student.id = '$userId'",
      )) ));
      
      $this->Course->Student->unbindModel(array( 'hasMany'=>array('TimeSlot', 'Lesson') ));
      if($courseId)
        $this->Course->Student->bindModel(array( 'hasAndBelongsToMany'=>array('Course'=>array(
          'conditions'=>"Course.id = '$courseId'")) ));
      
      $this->Course->unbindModel(array( 'hasAndBelongsToMany' => array('Period') ));
      
      if(!$period = $this->Course->Period->find('first', array('conditions'=>array('id'=>$periodId), 'recursive'=>3) )) {
        $this->Session->destroy(); // :security
        $this->redirect('/'); return;
      }
//pr($period); // debug
      
      $this->set('isStudent', true);
      $this->set('courses', $period['Student'][0]['Course']);
    }
    elseif($userType == 'faculty') {
      $this->Course->Faculty->unbindModel(array(
        'hasMany'=>array('TimeSlot', 'Lesson'),
        'hasAndBelongsToMany'=>array('Period'), ));
      $this->Course->unbindModel( array('hasAndBelongsToMany'=>array('Student', 'Period')) );
      if(!$instructor = $this->Course->Faculty->find('first', array('conditions'=>array('id' => $userId), 'recursive'=>2)) ) {
        $this->Session->destroy(); // :security
        $this->redirect('/'); return;
      }
      foreach($instructor['Course'] as $c=>$course)
        $instructor['Course'][$c]['Faculty'][0] = $instructor['Faculty'];
//pr($instructor); // debug
      if(!$period = $this->Course->Period->find('first', array('conditions'=>array('id'=>$periodId), 'recursive'=>0) )) {
        $this->Session->destroy(); // :security
        $this->redirect('/'); return;
      }
      
      $this->set('instructor', $instructor['Faculty']);
      $this->set('courses', $instructor['Course']);
    }
    
    $this->set('currentPeriod', array('Period'=>$period['Period']));
  }

	
	
	/** @todo this
	 * @param int $courseId
	 */
	function section($courseId) {
	  $this->Course->unbindModel(array( 'hasAndBelongsToMany'=>array('Period') ));
	  $this->Course->id = $courseId;
	  $course = $this->Course->read();
    $this->set('section', $course);
	}
	
	
	
	
	
	
	
	
	/** Lists all the courses in the system.
	 * may also process a search xxx: filter, order, paginate
	 * View at /admin/courses or /admin/courses/index
	 * @param string $courseId (in $this->params['id']) course number to find
	 */
	function admin_index($courseId = null) { if(!$courseId and isset($this->params['id'])) $courseId = $this->params['id'];
		$this->pageTitle = 'Courses Mgmt';
		
		if(isset($this->data['Course']['title'])) {	// search data sent:
			
			// verify search parameters sent:
			$empty = true; foreach($this->data['Course'] as $q=>$qwerty) {
				$this->data['Course'][$q] = trim($this->data['Course'][$q]);
				if(!empty($this->data['Course'][$q])) $empty = false;
			} if($empty) $this->Session->setFlash('Please use at least one search parameter.'); // xxx: after this could stop search behavior...
			
			// prepare search conditions: (used later)
			if(empty($this->data['Course']['area']) and empty($this->data['Course']['number']) and empty($this->data['Course']['section']))
				$id = false;
			if(empty($this->data['Course']['area'])) $this->data['Course']['area'] = '%';
			if(!isset($id)) $id = $this->data['Course']['area'].'.%'.$this->data['Course']['number'].'%.%'.$this->data['Course']['section'];
			if(empty($this->data['Course']['title'])) $title = false;
			else $title = '%'.$this->data['Course']['title'].'%';
			
			if($id) $conditions['or']['Course.id LIKE'] = $id;
			if($title) $conditions['or']['Course.title LIKE'] = $title;
			
			$this->set('clearCoursesSearch', true);
		}
		
		// xxx: filter data
		
		// xxx: order data
		
		// get & display courses (results):
		
		$periodId = $this->Session->read('Period.id');
		if($courseId) {
			$this->Course->Period->bindModel(array( 'hasAndBelongsToMany'=>array('Course'=>array(
				'conditions'=>"Course.id = '$courseId'", 'limit'=>50)) ));
				$this->set('noSearchFilter', true);
		} elseif(isset($conditions))
			$this->Course->Period->bindModel(array( 'hasAndBelongsToMany'=>array('Course'=>array(
				'conditions'=>$conditions, 'limit'=>50)) ));
		else
			$this->Course->Period->bindModel(array( 'hasAndBelongsToMany'=>array('Course'=>array('limit'=>50)) ));
		
		$this->Course->unbindModel(array(
			'hasAndBelongsToMany' => array('Student')	// not interested in students registered right now
		));
		$this->Course->Period->unbindModel(array('hasAndBelongsToMany'=>array('Student')));
		$period = $this->Course->Period->find('first', array(
			'conditions'=>array('id'=>$periodId),
			'recursive'=>2 ));
		
		$this->set('currentPeriod', array('Period'=>$period['Period']));
		
		$this->set('courses', $period['Course']);
		if(count($period['Course']) == 50) $this->set('maxResults', true);
		
//pr($period);
		// fill filter parameters options:
		foreach($period['Course'] as $course) {
			if(!empty($course['Faculty'])) foreach($course['Faculty'] as $instructor)
				$instructors[$instructor['id']] = $instructor['first_name'].' '.$instructor['last_name'];
			
			$n = explode('.', $course['id']);
			if(!isset($areas[$n[0]])) $areas[$n[0]] = 1;
			if(!isset($cNumbers[$n[1]])) $cNumbers[$n[1]] = 1;
			if(!isset($sections[$n[2]])) $sections[$n[2]] = 1;
			
			foreach($course['TimeSlot'] as $timeSlot)
				$locations[$timeSlot['location']] = 1;
			// xxx: sort $locations
		}
		
		$this->set('instructors', isset($instructors)?$instructors:array());
		$this->set('areas', isset($areas)?$areas:array());
		$this->set('cNumbers', isset($cNumbers)?$cNumbers:array());
		$this->set('sections', isset($sections)?$sections:array());
		$this->set('times', array('before 11AM', 'before 5PM', '5PM and after'));
		$this->set('locations', isset($locations)?$locations:array());
		$this->set('days', array('S', 'M', 'T', 'W', 'R', 'F', 'D'));
	}
	
	
	/** @todo Deletes selected courses from the database.
	 * No view for this one, POST to /admin/courses/delete
	 */
	function admin_delete() {
		
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Uploads the faculty-course time availability spreadsheet file.
	 * No view for this one, POST to /admin/courses/load
	 * 
	 * THIS IS OBJECT ADDITIVE (see /admin/users/load).
	 */
	function admin_load() {
		$this->autoRender = false;
//pr($this->data);	// debug
		
		// validation: max file upload size (should be 12MB):
		if($errorNumber = $this->data['Course']['spreadsheet_file']['error']) {
			if($errorNumber < 3) $this->Session->setFlash("The file {$this->data['Course']['spreadsheet_file']['name']} is too large.", null, array(), 'error');
			else if($errorNumber == 4) $this->Session->setFlash("No file sent...", null, array(), 'warning');
			else $this->Session->setFlash("An error occured while uploading the file {$this->data['Course']['spreadsheet_file']['name']}. Please try again.", null, array(), 'error');
//echo 'no file sent / file too large / transfer error';	// debug
			$this->redirect('/admin/courses');	// no file sent / file too large / transfer error
			return;
		}
		
		$ExcelReader = new Spreadsheet_Excel_Reader();
		$readFile = $ExcelReader->read($this->data['Course']['spreadsheet_file']['tmp_name']);	// xx: limit to 8 rows?
		// validation: file format:
		if(!$readFile) {	// file not supported:
//echo 'Unreadable file.';	// debug
			$this->Session->setFlash("The file {$this->data['Course']['spreadsheet_file']['name']} is not a spreadsheet.", null, array(), 'error');
			$this->redirect('/admin/courses'); return;	// strange file!
		}
		
//echo 'File read!';	// debug
		// validation: spreadsheet data format:
		if(!isset($ExcelReader->sheets[0])) $formatValid = false;	// On the 1st spreadsheet of the file
		if($ExcelReader->sheets[0]['numCols'] < 9) $formatValid = false;	// there should be 8 columns.
		$cells = $ExcelReader->sheets[0]['cells'];
		$cellCount = count($cells);
		if($cellCount < 2) {	// Data starts in the 2nd row.
			$this->Session->setFlash('The spreadsheet has no information.', null, array(), 'warning');
			$this->redirect('/admin/courses'); return;	// no data!
		}
		if(isset($formatValid) and !$formatValid) {
			$this->Session->setFlash('The spreadsheet doesn\'t have the appropiate format.', null, array(), 'error');
			$this->redirect('/admin/courses'); return;	// strange data!
		}
		
		
		// extract information from read, valid spreadsheet:
//pr($cells);	// debug;
		$faculty = array(); $facultyAux = array();
		$course = array(); $courseAux = array();
		$courses_periods = array();
		$courses_faculty = array();
		$timeSlots = array();
		
		$periodId = $this->Session->read('Period.id');
		
		for($c=2; $c<=$cellCount; $c++) {
//pr($cells[$c]);	// debug;
			
			// todo: validate major conflicts! xxx: count inserts to output in message
			
			// xxx: catch repeated id overwriting other users:
			$k = substr($cells[$c][3], 0, 8).'.'.substr($cells[$c][3], 8, 3);	// courses id includes section number
			$f = $cells[$c][2]; // faculty id is NYU ID number
			$newRelationship = false;
			
			if(empty($facultyAux[$f])) { $newRelationship = true;
				$facultyAux[$f]['type'] = 'faculty';
				$instructorName = explode(',', $cells[$c][1]);
				$facultyAux[$f]['first_name'] = $instructorName[1];
				$facultyAux[$f]['last_name'] = $instructorName[0];
//				$facultyAux[$f]['email'] = $cells[$c][9];	// xxx this
//echo $f; pr($facultyAux[$f]); // debug;
			}
			
			if(empty($courseAux[$k])) { $newRelationship = true;
				$courseAux[$k]['title'] = $cells[$c][4];
//echo $k; pr($courseAux[$k]); // debug;
				
				if($cells[$c][5] != 'TBA') for($d=0; $d<strlen($cells[$c][5]); $d++) {	// xxx: days validation
					$timeSlots[] = array( 'TimeSlot' => array(
						'faculty_id' => $f,
						'course_id' => $k,
						'period_id' => $periodId,
						'day' => $cells[$c][5][$d],	// todo: make 1 per day
						'start_time' => $cells[$c][6],
						'end_time' => $cells[$c][7],
						'location' => $cells[$c][8].' '.$cells[$c][9],
					));
//echo 'ts'; pr($timeSlots[count($timeSlots)-1]); // debug;
				}
				
				$courses_periods[] = array('course_id' => $k, 'period_id' => $periodId);
//echo 'c-p'; pr($courses_periods[count($courses_periods)-1]); // debug;
			} else ;	// xxx: integrity:
				//if($courseAux[$k]['title'] != $cells[$c][3]) ;	// name is different
			
			// build faculty - course relationship:
			if($newRelationship) $courses_faculty[] = array('CoursesUser'=>array('course_id'=>$k, 'user_id'=>$f));
//echo 'c-f'; pr($courses_faculty[count($courses_faculty)-1]); // debug;
		}
		
		// transform arrays and load data:
		foreach($facultyAux as $f=>$facMember)
			$faculty[] = array( 'Faculty' => array(
				'id' => $f,
				'type' => 'faculty',
				'first_name' => $facMember['first_name'],
				'last_name' => $facMember['last_name'],
				//'email' => $facMember['email'],
			));
		foreach($courseAux as $k=>$aCourse)
			$course[] = array( 'Course' => array(
				'id' => $k,
				'title' => $aCourse['title'],
			));
		
//pr($course);	// debug
//pr($courses_periods);	// debug
//pr($faculty);	// debug
//pr($courses_faculty);	// debug
//pr($timeSlots);	// debug
		
		// load data:
		unset($this->Course->User->validate['email']);
		unset($this->Course->User->validate['telephone']);
		unset($this->Course->User->validate['status']);
		
		$this->Course->saveAll($course, array('validate' => 'first', 'atomic'=>false));
		$this->Course->Faculty->saveAll($faculty, array('validate' => 'first', 'atomic'=>false));
		// todo: only save if both user & course loaded.
		// xxx: manage Cake warning when repeating values (instead of ob_ trick)?
ob_start();
		$this->Course->CoursesPeriod->saveAll($courses_periods, array('validate' => 'first', 'atomic'=>false));
		$this->Course->CoursesUser->saveAll($courses_faculty, array('validate' => 'first', 'atomic'=>false));
		$this->Course->Faculty->TimeSlot->saveAll($timeSlots, array('validate' => 'first', 'atomic'=>false));
$output = ob_get_clean();
//echo $output;	// debug
		
//pr($this->Course->invalidFields());	// debug
//pr($this->Course->CoursesPeriod->invalidFields());	// debug
//pr($this->Course->Faculty->invalidFields());	// debug
//pr($this->Course->CoursesUser->invalidFields());	// debug
//pr($this->Course->Faculty->TimeSlot->invalidFields());	// debug
		
		$this->Session->setFlash('Data uploaded! ('.($c-2).' records processed)', null, array(), 'success');
		$this->redirect('/admin/courses');
	}
}
?>