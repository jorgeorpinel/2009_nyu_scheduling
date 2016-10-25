<?php
/* NYU Steinhardt Private Lessons Scheduling Sysyem
 * Built with CakePHP 2 (PHP5) and MySQL
 */

// xxx: use CakePHP vendor import instead:
require_once '../../vendors/excel/reader.php';

/** Users functionality
 * @copyright (c) New York University 2009
 * @author Jorge Orpinel
 */
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Javascript');
	var $components = array('Authentication');



	/** This resets the password for a given user. It's the only option since the system does not know
	 * the original sting stored in MD5.
	 * View at /users/password/[NYU ID Number]
	 * @param int $userId NYU ID Number of the user to reset password for
	 */
	function password($userId = null) {
		if($this->Session->check('User.id')) {$this->redirect('/'); return;  }

		$this->autoRender = false;

		$user = $this->User->find('first', array('conditions'=>array('id' => $userId), 'fields'=>'id, type, first_name, last_name, email', 'recursive'=>0));

		// validate email JIC:	// xx: test actual email existance first?
		if(!filter_var($user['User']['email'], FILTER_VALIDATE_EMAIL)) {	// PHP >= 5.2
		/* x: In case PHP < 5.2 try:
		$pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
			'(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
		if(preg_match ($pattern, $user['User']['email'])) {
		*/
			$this->Session->setFlash("We don't have a valid email address to contact you... Please contact the music department.", null, array(), 'warning');
			$this->render(); return;
		}

		// reset password:
		if($user) {
			$user['User']['password'] = preg_replace('/([ ])/e', 'chr(rand(97,122))', '        ');
			if(!$this->User->save(array('User'=>array('id'=>$user['User']['id'], 'password'=>$user['User']['password']))))
				$this->Session->setFlash('Unexpected error. Please try again.', null, array(), 'error');	// xx: should never happen
//pr($user);	// debug
		}

		// send password to user's email:
		$to = $user['User']['email'];
		$subject = "Your NYU Scheduling Password";
		if($user['User']['type'] == "faculty") $user['User']['type'] = "instructor";
		// xxx: use Cake layout and view (HTML insted of plain text):
		$body =	"Dear {$user['User']['type']} {$user['User']['first_name']} {$user['User']['last_name']}:\n\n".
						"Your new password has been generated for the NYU Private Lessons Scheduling System.\n".
						"\n\t{$user['User']['password']}\t\n\n";

		if (@mail($to, $subject, $body))  // xxx: remove @?
			$this->Session->setFlash("Email sent to {$user['User']['email']}.", null, array(), 'success');
		else {
			$this->Session->setFlash('Unexpected emailing error. Please try again.', null, array(), 'warning');
pr($user); // debug
		}

		$this->redirect('/');
	}


	/** Performs login
	 * View at /login or /users/login
	 */
	function login() {
		if($this->Session->check('User.id')) {$this->redirect('/'); return;  }

		$this->pageTitle = 'Sign In';
		if (empty($this->data)) {
			$this->_postFail();	// sets periods and current
			$this->render();
			return;
		}

		$options = array(	// Authentification component options
			'userModelPtr' => &$this->User,
			'username' => 'id',
			'password' => 'password',
			'encoding' => 'md5',
			'avoidReturn' => true,
			'failed' => array(
				'postFailure'=> '_postFail',
				'invalidate' => 'information',
				'renderView' => true
			),
			'succeded' => array(
				'postLogin' => '_postLogin'
			),
			'userId' => 'User.id'
		);
		$this->Authentication->login($options);
	}

	/** Auxiliary function for succesful login()
	 * No view for this one
	 */
	function _postLogin() {
		if($this->Session->check('User.id')) {
			$userId = $this->Session->read('User.id');
			$user = $this->User->find('first', array('conditions'=>array('id'=>$userId), 'fields'=>'type, first_name, last_name', 'recursive'=>0));

			// if student check that he is active for the period:	// xxx: faculty too?
			if($user['User']['type'] == 'student') {
				$periodsStudent = $this->User->query('SELECT `period_id`, `1st_login` FROM `periods_students` AS `PeriodsStudent`'
					." WHERE `period_id`='{$this->data['Period']['id']}' AND `student_id`='$userId' LIMIT 1");	// xx: custom query necesary?

				if(empty($periodsStudent)) {
					$this->Session->destroy();	// cancel login
					$this->Session->setFlash('You are not active for the selected period. Please contact the department.', null, array(), 'error');
					$this->redirect('/'); return;	// start over
				} else
					// period should exsit:
					if(!$period = $this->User->Period->find('first', array(
						'conditions'=>array('Period.id'=>$this->data['Period']['id']),
						'recursive'=>-1,
						'fields'=>'id'))) {
						$this->Session->destroy();	// cancel login
						$this->redirect('/'); return;	// :security
					}
			}

			$this->Session->write('User.type', $user['User']['type']);
			$this->Session->write('User.name', $user['User']['first_name'].' '.$user['User']['last_name']);
			$this->Session->write('Period.id', $this->data['Period']['id']);
			// $this->Session->write('Period.year', $this->data['Period']['year']); // TODO

			// redirects according to user type:
			if($user['User']['type']=='student') // check if 1st login for the period:
			  if($periodsStudent[0]['PeriodsStudent']['1st_login']) $this->redirect('/users/verify_contact');
			  else $this->redirect('/my_courses');

			if($user['User']['type'] == 'faculty') $this->redirect('/faculty/schedule/'.$userId);
			else $this->redirect('/courses');
		}
	}

	/** Auxiliary function for failed login() in case login is being rendered.
	 * Chooses te current period and gives the option to choose the comming ones to
	 * No view (part of /users/login)
	 */
	function _postFail() {
//		$this->User->Period->find('list', array('recursive'=>0, 'fields'=>array('id', 'type','year')));	// auto-list
		$periods = $this->User->Period->find('all', array(
		'recursive'=>0,
		'conditions' => 'ends > CURDATE() or starts < CURDATE()',
		'order'=>'starts',
		));

		$periodList = array();
		foreach($periods as $period)
			$periodList[$period['Period']['year']][$period['Period']['id']] = $period['Period']['type'];

		$this->set('periods', $periodList);
		$this->set('currentPeriod', !empty($this->data['Period']['id'])?$this->data['Period']['id']:$periods[0]['Period']['id']);
	}


	/** Used at 1st login each period to offer user a chance to verify his contact info.
	 * View @ /users/verify_contact
	 */
	function verify_contact() {
		$periodId = $this->Session->read('Period.id');
		$userId = $this->Session->read('User.id');
		$periodsStudent = $this->User->query('SELECT `1st_login` FROM `periods_students` AS `PeriodsStudent`'
		." WHERE `period_id`='".$periodId."' AND `student_id`='".$userId."' LIMIT 1");	// xx: custom query necesary?
		if(!$periodsStudent[0]['PeriodsStudent']['1st_login']) $this->redirect('/');	// :access control

		if(empty($this->data)) {	// show view:
			$this->data = $this->User->find('first', array('conditions'=>array('id'=>$userId), 'fields'=>'email, telephone', 'recursive'=>0));
		} else {	// save verified data:
			$this->data['User']['id'] = $userId;
			if($this->User->save($this->data, array('fields'=>array('User'=>array('id', 'email', 'telephone'))))) {
				$this->Session->setFlash('Your contact information has been updated.');

				// xx: this is not a transaction, could result in contact info.
				$this->User->query("UPDATE `periods_students` SET `1st_login` = '0' "
				." WHERE `period_id`='".$periodId."' AND `student_id`='".$userId."' LIMIT 1");	// xx: custom query necesary?
				$this->redirect('/');
			}
		}
	}


	/** Destroys the user session and returns to login page
	 * No view for this one
	 */
	function logout() {
		if ($this->Session->check('User.id')) {
			$options = array(
			'avoidReturn' => true,
			'sessionFlash' => 'See you soon!',
			'URL' => '/login');
			$this->Authentication->logout($options);
		}
		else $this->redirect('/login');
	}



	/** Shows user's schedule in a calendar
	 * View /admin/:type/schedule/[userId] being type = students|faculty|users
	 * @param int $userId id of user to view schedule for.
	 * @param int $timeSlotId id of the course on top of the schedule display
	 */
	function schedule($userId, $timeSlotId = null) {
		// data preparation:
		$periodId = $this->Session->read('Period.id');
		$urlParts = explode('/', $this->params['url']['url']);


		if($urlParts[0] == 'students') {	$rec = 3;																// student's schedule

			// get this period's courses of this student:
			$courses = $this->User->Period->query(	// (1) double, nested JOIN to get this period's timeslots ids
				"SELECT Course.id"
				." FROM courses as Course JOIN courses_periods as CP ON (Course.id = CP.course_id)\n"
					." JOIN periods as P ON (CP.period_id = P.id AND P.id = $periodId)\n"
				// so far we have this period's courses
				." JOIN "	// {
					// this inner join gives us the user's courses...
					." (courses_users as CS JOIN users as S ON (CS.user_id = S.id AND S.id = '$userId'))"
				." ON (Course.id = CS.course_id)\n"	// } and from there we get only this period's courses
				.';'	// (for the user)
			);
			if(empty($courses)) {
				$this->Session->setFlash('This student is not registered to any course.', null, array(), 'Error');
				$this->redirect("/admin/students/$userId"); return; }	// :security
			foreach($courses as $c)
				$cIds[] = "'".$c['Course']['id']."'";	// (1) used to condition timeslot relationship with user
			$cIds = implode(', ', $cIds);
//pr($cIds);	// debug

			// User
			$this->User->unbindModel(array(
				'hasMany' => array('TimeSlot', 'Lesson'),
				'hasAndBelongsToMany' => array('Period')	// leave courses
			));
			$this->User->bindModel(array(
				'hasAndBelongsToMany' => array('Course'=>array('conditions'=>"Course.id IN ($cIds)"))
			));
			// Course
			$this->User->Course->unbindModel(array(
				'hasAndBelongsToMany' => array('Faculty', 'Student', 'Period')
			));
			// TimeSlot
			$this->User->Course->TimeSlot->unbindModel(array(
				'belongsTo' => array('Course'),	// leave faculty member (and hasMany lessons)
			));
			$this->User->Course->TimeSlot->bindModel(array(	// only get this user's lessons :)
				'hasMany' => array('Lesson'=>array('conditions'=>array('student_id'=>$userId))),
			));
		}


		if($urlParts[0] == 'faculty') {	$rec = 2;															// instructor's schedule

			// get this period's time slots of this instructor:
			$timeSlots = $this->User->Period->query(	// (1) double, nested JOIN to get this period's timeslots ids
				"SELECT TimeSlot.id FROM courses_periods as CoursesPeriod JOIN"
					." (courses AS Course JOIN time_slots as TimeSlot ON (Course.id = TimeSlot.course_id AND TimeSlot.faculty_id = '$userId'))"
				." ON (CoursesPeriod.course_id = Course.id)"
				." WHERE CoursesPeriod.period_id = $periodId"
			);
			if(empty($timeSlots)) { $this->cakeError('error404'); return; }	// :security
			foreach($timeSlots as $ts)
				$tsIds[] = $ts['TimeSlot']['id'];	// (1) used to condition timeslot relationship with user
			$tsIds = implode(', ', $tsIds);
//pr($timeSlots);	// debug

			// User
			$this->User->unbindModel(array(
				'hasMany' => array('Lesson'),
				'hasAndBelongsToMany' => array('Course'), // (1) have to be obtained from time-slots, for the current period only
			));
			$this->User->bindModel(array(
				'hasMany' => array('TimeSlot'=>array(
					'foreignKey' => 'faculty_id',
					'conditions'=>"TimeSlot.id IN ($tsIds)",
			)) ));
			$this->User->TimeSlot->unbindModel(array('belongsTo'=>array('Faculty')));	// we know that already...
		}



		// data validation:
		$user = $this->User->find('first', array('conditions'=>array('id'=>$userId), 'recursive'=>$rec));
		if(!$user or $user['User']['type']=='admin') { $this->cakeError('error404'); return; }	// :security
//pr($user);	// debug

		// set marked (active) time-slot id:
		if($urlParts[0] == 'students')
			$this->set('ot', $timeSlotId?$timeSlotId:$user['Course'][0]['TimeSlot'][0]);

		if($this->Session->read('User.type') != 'admin')	// accses control: _may_only_see_own_schedule_
			if($this->Session->read('User.id') != $userId) { $this->cakeError('error404'); return; }

		if($user['User']['type'] == 'student') {
			if($urlParts[0] != 'students') { $this->cakeError('error404'); return; }	// :security
			$type = 'Student';
		} elseif($user['User']['type'] == 'faculty') {	// should be faculty type if database is congruent
			if($urlParts[0] != 'faculty') { $this->cakeError('error404'); return; }	// :security
			foreach($user['TimeSlot'] as $t=>$timeSlot)	// period congruency check xx: necesary ?
				if(empty($timeSlot['Course'])) unset($user['TimeSlot'][$t]);
			$type = 'Professor';
		} else { $this->cakeError('error404'); return; }	// :security (DB incongruency, this shouldn't happen)

		$this->pageTitle = 'Schedule for '.$type.' '.$user['User']['first_name'].' '.$user['User']['last_name'];
		if($type == 'Student') $this->pageTitle .= ' '.$user['User']['id'];

		$period = $this->User->Period->find('first', array('conditions'=>array('Period.id'=>$periodId), 'fields'=>'type, year', 'recursive'=>0));

		$this->set('user', $user);
		$this->set('period', $period['Period']['type'].' '.$period['Period']['year']);
		$this->set('days', array('M'=>1,'T'=>2,'W'=>3,'R'=>4,'F'=>5,'S'=>6));
		$this->set('today', date('w', time()));

		$this->set('type', $type);
	}


	/** @todo this
	 * @param int $userId Id number (NYU) of the user to display
	 */
	function report($userId) {
		// todo: find user with type and set pageTitle:
		$this->pageTitle = '';

	}


	/** Edits a user @todo active periods for student (if admin editing)
	 * @param $editId User NYU id to edit
	 */
	function edit($editId = null) {
    $userType = $this->Session->read('User.type');
	  $userId = $this->Session->read('User.id');

	  if ($editId == null or $userType != 'admin') $editId = $userId; // user (student, faculty) can only edit himself

	  // get edited user & do basic DB consistency validation:
	  if(!$edit = $this->User->find('first', array('conditions'=>array('id'=>$editId), 'recursive'=>0) )) {
	    if($userId == $editId) // weird situation...
	      { $this->Session->destroy(); $trhis->redirect('/'); return; } // :security (shouldn't happen)
	  //else
	    $this->cakeError('error404'); return; // wrong user id passed, apparently (should only happen with admins)
	  } elseif($userId==$editId and $edit['User']['type']!=$userType) // weird situation...
      { $this->Session->destroy(); $this->redirect('/'); return; } // :security (shouldn't happen)

    $this->pageTitle = "Edit Information for {$edit['User']['first_name']} {$edit['User']['last_name']}";

    if(empty($this->data)) { // set the view only:

      $this->data = $edit;
      $this->data['User']['password'] = null;
      if($this->data['User']['type'] == 'students') $this->data['User']['type'] .= 's';

    } else { // edit data sent: edit! (save)

      // prepare data for save:
//pr($this->data); / debug
      unset($this->data['User']['id']);   // unchangable (not a parameter, shouldn't even be sent)
      unset($this->data['User']['type']);   // unchangable
      unset($this->data['User']['status']); // uncangable
      foreach($this->data['User'] as $d=>$datum)
        $data['User'][$d] = trim($datum);   // trim strings
      if(empty($data['User']['password'])) unset($data['User']['password']); // dont change if empty sent
      $data['User']['id'] = $editId;
//pr($data); // debug

      // save:
      if($this->User->save($data))
        $this->Session->setFlash('The information has been updated.', array(), null, 'success');
      else // should never happen...
        $this->Session->setFlash('Unexpeceted database error. Please try again.', array(), null, 'error');

      $this->data = $data;
      $this->data['User']['password'] = null;
      $this->data['User']['type'] = $edit['User']['type'];
      $this->data['User']['status'] = $edit['User']['status'];
      if($this->data['User']['type'] == 'students') $this->data['User']['type'] .= 's';
    }
	}








	/** Changes the current working period
	 * Should be used by AJAX
	 */
	function admin_period($periodId) {
		$this->autoRender = false;
		if(!$period = $this->User->Period->find('first', array('conditions'=>array('id'=>$periodId))))
			echo 'false';

	}


	/** Shows the students and the classes they are registered for
	 * View at /admin/students or /admin/students/index
	 * @param int $type Type of users to show.
	 * @param string $userId (may be in $this->params['id']) User id or NYU ID
	 */
	function admin_index($type = 'administrators', $userId = null) { if(isset($this->params['id'])) $userId = $this->params['id'];
		$this->pageTitle = 'User Mgmt: '.ucwords($type);

		$this->set('type', $type);

		if($userId) {
			// todo: check perdiod!
			$user = $this->User->find('first', array('conditions'=>array("User.id = '$userId'")));
			if($user) $user = array(0=>$user);
			$this->set('noSearchFilter', true);
		}

		else	// cant search and specify a user id.

		if(isset($this->data['User']['name'])) {	// search data sent:
			switch($type) {	// search only for student/instructor
				case 'students': $user = 'Student'; break;	// case avoids default behavior
				case 'faculty': $user = 'User'; break;
				default: $this->redirect('/admin/'.$type); return;	// :security
			}

			// verify search parameters sent:
			$empty = true; foreach($this->data['User'] as $q=>$qwerty) {
				$this->data['User'][$q] = trim($this->data['User'][$q]);
				if(!empty($this->data['User'][$q])) $empty = false;
			} if($empty) {
				$this->Session->setFlash('Please use at least one search parameter.'); // xxx: after this could stop search behavior...
			}

			// prepare search conditions: (used later)
			if(empty($this->data['User']['id'])) $id = false;
			else $id = '%'.$this->data['User']['id'].'%';
			if(empty($this->data['User']['name'])) $name = false;
			else $name = '%'.$this->data['User']['name'].'%';

			if($id) $conditions['or']["{$user}.id LIKE"] = $id;
			if($name) {
				$conditions['or']["{$user}.first_name LIKE"] = $name;
				$conditions['or']["{$user}.last_name LIKE"] = $name;
			}

			if($type == 'students') $this->set('clearStudentsSearch', true);
			elseif($type == 'faculty') $this->set('clearFacultySearch', true);
		}

		if($type == 'students') {
			$conditions['Student.type'] = 'student';
			if(!$userId) {
				$this->User->Period->unbindModel(array('hasMany'=>array('Course')));
				$this->User->Period->bindModel(array('hasAndBelongsToMany'=>array(
					'Student'=>array(
						'className' => 'User',
						'joinTable' => 'periods_students',
						'associationForeignKey' => 'student_id',
						'with' => 'PeriodsStudent',
						'conditions' => $conditions,
				))));
				$this->User->unbindModel(array('hasMany' => array('TimeSlot')));	// sohuldn't have them anyways
				$period = $this->User->Period->find('first', array(
					'conditions'=>array('id'=>$this->Session->read('Period.id')), 'recursive'=>2));
				$this->set('currentPeriod', array('Period'=>$period['Period']));
			}
			$this->set('users', $userId ? $user : $period['Student']);

			$this->set('currentPeriod', $period = $this->User->Period->find('first', array(
				'conditions'=>array('id'=>$this->Session->read('Period.id')), 'recursive'=>0)));
		} else {
			// instructors or administrators (not period-bounded):
			$conditions['User.type'] = $type=='administrators'?'admin':$type;
			$this->set('users', $userId ? $user : $this->User->find('all', array('conditions'=>$conditions)) );

			$this->set('currentPeriod', $period = $this->User->Period->find('first', array(
				'conditions'=>array('id'=>$this->Session->read('Period.id')), 'recursive'=>0)));
		}
	}


	/** Used to register a student to a course individualy OR
	 * @todo: assigns an instructor to a course
	 * View at /admin/students/register/$userId or /admin/users/register/$userId
	 * @param int $userId The student/instructor's NYU ID
	 */
	function admin_register($userId) {
		$this->User->unbindModel(array('hasMany'=>array('TimeSlot', 'Lesson')));
		$this->User->bindModel(array('hasAndBelongsToMany'=>array(
			'Period' => array(
				'joinTable' => 'periods_students',
				'foreignKey' => 'student_id',
				'conditions' => 'Period.id = '.$this->Session->read('Period.id'),
			),
		)));
		if(!$student=$this->User->find('first', array('conditions'=>"User.id = '$userId'")) or $student['User']['type']!='student')
			{$this->cakeError('error404'); return;}	// :security

		$this->pageTitle = 'Register Student to a Course';
		$periodId = $this->Session->read('Period.id');
		$this->set('student', $student);

		if(empty($this->data)) return;	// :no course selected yet

//pr($this->data);	// debug
		if(empty($this->data['Course']['id'])) {	// search data sent:

			// verify search parameters are not empty:
			$empty = true;
			foreach($this->data['Course'] as $q=>$qwerty) {
				$this->data['Course'][$q] = trim($this->data['Course'][$q]);
				if(!empty($this->data['Course'][$q])) $empty = false;
			} if($empty) { $this->Session->setFlash('Please use at least one search parameter.'); return; }

			// prepare search conditions:
			if(empty($this->data['Course']['area']) and empty($this->data['Course']['number']) and empty($this->data['Course']['section']))
				$id = false;

			if(empty($this->data['Course']['area'])) $this->data['Course']['area'] = '%';

			if(!isset($id)) $id = $this->data['Course']['area'].'.%'.$this->data['Course']['number'].'%.%'.$this->data['Course']['section'];

			if(empty($this->data['Course']['title'])) $title = false;
			else $title = '%'.$this->data['Course']['title'].'%';

			$conditions = ($id ? "Course.id LIKE '$id'" : '1=0').($title ? " OR Course.title LIKE '$title'" : null);

			// xxx: limit result number?
			$this->User->Period->bindModel(array( 'hasAndBelongsToMany'=>array('Course'=>array('conditions'=>$conditions )) ));
			$this->User->Period->unbindModel(array('hasAndBelongsToMany'=>array('Student')));	// we dont care about students now
			$period = $this->User->Period->find('first', array('conditions'=>array('id'=>$periodId)) );

			// filter out courses the student is registered for already:
			foreach($period['Course'] as $c=>$course) {
				$period['Course'][$course['id']] = $course;
				unset($period['Course'][$c]);
			}
			foreach($student['Course'] as $course)
				unset($period['Course'][$course['id']]);

			if(!empty($period['Course'])) { $this->set('courses', $period['Course']); return; }
			else { $this->Session->setFlash('No courses found.', null, array(), 'warning'); return; }
		}


		// course id sent for registration:

		if($this->data['Course']['id'] == 'null')
			{ $this->Session->setFlash('Please select a course from the options list (return with the BACK button on your browser).', null, array(), 'warning'); return; }

		$credits = false;
		// xxx:BUG: if .99999999999999 sent, PHP think its >= 1! but MySQL may save as 0:
		if(!empty($this->data['Course']['credits']) and is_numeric($this->data['Course']['credits']))
			if($this->data['Course']['credits']==2 or $this->data['Course']['credits']==($student['User']['status']=='m'?3:4))
				$credits = $this->data['Course']['credits'];
		if($credits) {

			// todo: check period:
			if(!$course = $this->User->Course->find('first', array('conditions'=>"id = '{$this->data['Course']['id']}'", 'fields'=>'title', 'revursive'=>0)))
				{ $this->redirect('/'); return; }	// :security\

ob_start();
			if($this->User->CoursesUser->save(array('CoursesUser'=>array('course_id'=>$this->data['Course']['id'], 'user_id'=>$userId, 'credits'=>$credits)))) {
				$this->Session->setFlash("The student was registered to {$course['Course']['title']}", null, array(), 'success');
				$this->redirect('/admin/students/register/'.$userId); return;
			} else {	// xx:system error   This should NEVER happen.
				$this->Session->setFlash('SORRY. THE SYSTEM IS HAVING TROUBLE CONNECTING TO THE DATABASE. <b>PLEASE TRY AGAIN.</b>', null, array(), 'error');
			}
$output = ob_get_clean();
//pr($output);	// debug
		if(!empty($output)) {	// PHP pinted a SQL Error Warning probably. This shouldn't occur naturally.
			$this->redirect('/'); return; // :security
		}

		} else {	// if ( ! $credits )
			$this->Session->setFlash('Wrong credits. Please try again (you may use the BACK button on your browser).', null, array(), 'error');
			$this->data['Course']['credits'] = null;
		}
	}


	/** Drops a student/instructor's link to a course
	 * If student, all lessons shceduled are deleted too
	 * @param $userId student/instructor's NYU ID
	 * @param $courseId course full number
	 */
	function admin_drop($userId, $courseId) {
		if(!$course_user = $this->User->CoursesUser->find('first', array('conditions'=>array('course_id'=>$courseId, 'user_id'=>$userId), 'fields'=>'id, course_id, user_id')))
			{ $this->redirect('/'); return; }	// :security
		if(!$user = $this->User->find('first', array('conditions'=>array('id'=>$userId), 'fields'=>'type, email', 'recursive'=>-1)))
			{ $this->redirect('/'); return; }	// :security	// xxx: this shouldn't happen since courses_user found...

		// xxx: a SQL transaction would be nice for this whole operation.

		// delete the relationship:
		if(!$this->User->CoursesUser->del($course_user['CoursesUser']['id'])) {	// xx:system error   This should NEVER happen.
			$this->Session->setFlash('SORRY. THE SYSTEM IS HAVING TROUBLE CONNECTING TO THE DATABASE. <b>PLEASE TRY AGAIN.</b>', null, array(), 'error');
			$this->redirect('/admin/users/register/'.$userId); return;
		}

		$this->autoRender = false;

		// find lessons for this user-course and kill them:
		$this->User->TimeSlot->bindModel(array('hasMany'=>array(
			'Lesson' => array('fields'=>'id')
		)));
		$timeSlots = $this->User->TimeSlot->find('all', array('conditions'=>"TimeSlot.course_id = '{$course_user['CoursesUser']['course_id']}'", 'fields'=>'id'));
		foreach($timeSlots as $timeSlot)
			foreach($timeSlot['Lesson'] as $lesson)
				$lessonIdsToDelete[] = $lesson['id'];

		if(!empty($lessonIdsToDelete))
			if(!$this->User->TimeSlot->Lesson->deleteAll('Lesson.id IN ('.implode(',', $lessonIdsToDelete).')')) {
				// xx: garbage generated... This should really never happen though.
				// what now? ignore for now, won't affect the system's functionality.
			} else;
		else $lessonIdsToDelete = array();	// to be able to count the 0 lessons dropped in following message...

		// xxx: email notice to appropriate users?
//		// validate email JIC:	// xx: test actual email existance first?
//		if(!filter_var($user['User']['email'], FILTER_VALIDATE_EMAIL)) {	// PHP >= 5.2
//		/* x: In case PHP < 5.2 checkout users_controller.php line #35 */
//			$this->Session->setFlash("We don't have a valid email address to contact you... Please contact the music department.", null, array(), 'warning');
//			$this->render(); return;
//		}
//
//		// send password to user's email:
//		$to = $user['User']['email'];
//		$subject = "Your NYU Scheduling Password";
//		if($user['User']['type'] == "faculty") $user['User']['type'] = "instructor";
//		// xxx: use Cake layout and view (HTML insted of plain text):
//		$body =	"Dear {$user['User']['type']} {$user['User']['first_name']} {$user['User']['last_name']}:\n\n".
//						"Your new password has been generated for the NYU Private Lessons Scheduling System.\n".
//						"\n\t{$user['User']['password']}\t\n\n";

		$this->Session->setFlash("The student's course registration was dropped from the system records. (".count($lessonIdsToDelete).' lessons unscheduled)', null, array(), 'success');
		$this->redirect('/admin/users/register/'.$userId);
	}


	/** Adds a new user @todo active periods
	 * NOTE: $type is taken from URL ($this->params['url']['url'])
	 */
	function admin_add() {
	  $type = explode('/', $this->params['url']['url']);
	  $urlType = $type[1];
	  $type = $type[1];
    if($type == 'users') $type = 'admin';
    elseif($type == 'students') $type = 'student';
	  if(!in_array($type, array('admin', 'student', 'faculty'))) {
	    $this->cakeError('error404'); return; } // :security

	  if(!empty($this->data)) { // todo: post data sent! create: (save)

	    if($type != 'admin') $this->Course->validation['id'] = array(
	     'rule' => '/^N[0-9]{8}$/i',
	     'message' => 'Invalid NYU ID number');
	    if($type != 'student') unset($this->Course->validation['status']);
	    // save:
	    if($this->User->save($this->data)) {
        $this->Session->setFlash(ucwords($type).' created!', array(), null, 'success');
	      $this->redirect("/admin/$urlType".($type!='admin'?"/{$this->User->id}":null)); return;
	    } else {
	      $this->Session->setFlash('Please correct the following errors:', array(), null, 'error');
	    }
	  }
	//else set the view:
	  $this->pageTitle = 'New '.($type=='admin'?'Administrator':($type=='faculty'?'Instructor':'Student'));
    $this->set('urlType', $urlType);
	  $this->set('type', $type);
	}


	/** @todo: Deletes a user/faculty/student and all of irs associated dependand records (lessons, time-slots, and courses)
	 * @param $userId User NYU id to edit
	 */
	function admin_delete($userId) {

	}








	/**
	 * Uploads the students and their courses relationships.
	 * No view for this one, POST to /admin/users/load
	 *
	 * THIS IS COURSE EXCLUSIVE (see /admin/courses/load).
	 */
	function admin_load() {
		$this->autoRender = false;
//pr($this->data);	// debug

		$periodId = $this->Session->read('Period.id');

		// security: validation: period existent:
		if(!$period = $this->User->Period->find('first', array('conditions'=>array('id'=>$periodId), 'fields'=>'id', 'recursive'=>0))) {
			$this->redirect('/admin/students'); return;	// unexistent period...
		}

		// validation: max file upload size (should be 12MB):
		if($errorNumber = $this->data['User']['spreadsheet_file']['error']) {
			if($errorNumber < 3) $this->Session->setFlash("The file {$this->data['User']['spreadsheet_file']['name']} is too large.", null, array(), 'error');
			else if($errorNumber == 4) $this->Session->setFlash("No file sent...", null, array(), 'warning');
			else $this->Session->setFlash("An error occured while uploading the file {$this->data['User']['spreadsheet_file']['name']}. Please try again.", null, array(), 'error');
//echo 'no file sent / file too large / transfer error';	// debug
			$this->redirect('/admin/students');	// no file sent / file too large / transfer error
			return;
		}

		$ExcelReader = new Spreadsheet_Excel_Reader();
@		$readFile = $ExcelReader->read($this->data['User']['spreadsheet_file']['tmp_name']);	// xxx: remove @ ?
		// validation: file format:
		if(!$readFile) {	// file not supported:
//echo 'Unreadable file.';	// debug
			$this->Session->setFlash("The file {$this->data['User']['spreadsheet_file']['name']} is not a spreadsheet.", null, array(), 'error');
			$this->redirect('/admin/students'); return;	// strange file!
		}

//echo 'File read!';	// debug
		// validation: spreadsheet data format:
		if(!isset($ExcelReader->sheets[0])) $formatValid = false;	// On the 1st spreadsheet of the file
		if($ExcelReader->sheets[0]['numCols'] < 5) $formatValid = false;	// there should be 5+ columns.
		$cells = $ExcelReader->sheets[0]['cells'];
		$cellCount = count($cells);
		if($cellCount < 2) {	// Data starts in the 2nd row.
			$this->Session->setFlash('The spreadsheet has no information.', null, array(), 'warning');
			$this->redirect('/admin/students'); return;	// no data!
		}
		if(isset($formatValid) and !$formatValid) {
			$this->Session->setFlash('The spreadsheet doesn\'t have the appropiate format.', null, array(), 'error');
			$this->redirect('/admin/students'); return;	// strange data!
		}



		// extract information from read, valid spreadsheet:
		$students = array(); $studentsAux = array();
		$students_period = array();

		for($c=2; $c<=$cellCount; $c++) {
//pr($cells[$c]);	// debug
			$s = $cells[$c][2];	// xxx: catch repeated id errors... THIS IGNORES REPEATS
			$k = substr($cells[$c][3], 0, 8).'.'.substr($cells[$c][3], 8, 3);

			if(!empty($studentsAux[$s])) {
				$studentsAux[$s]['Course'][] = array('course_id' => $k, 'user_id' => $s);
				continue;	// jumps to next "for" iteration
			}

			$studentName = explode(',', $cells[$c][1]);
			$studentsAux[$s] = array(	// xxx: The 1st registry accounts only for student data.
				'first_name' => $studentName[1],
				'last_name' => $studentName[0],
				'email' => empty($cells[$c][6])?null:$cells[$c][6],
				'telephone' => empty($cells[$c][7])?null:$cells[$c][7],
				'status' => $cells[$c][5],
				'Course' => array( 0 => array(
					'course_id' => $k,
					'user_id' => $s,
					'credits' => $cells[$c][4],
				)),
			);
//pr($studentsAux[$s]);	// debug

			// user - period relationship:
			$students_period[] = array('PeriodsStudent'=>array('period_id' => $periodId, 'student_id' => $s, '1st_login'=>1));
//pr($students_period[count($students_period)-1]);	// debug
		}

		foreach($studentsAux as $s=>$student)
			$students[] = array(
				'User' => array(
					'id' => $s,
					'type' => 'student',
					'first_name' => $student['first_name'],
					'last_name' => $student['last_name'],
					'email' => $student['email'],
					'telephone' => $student['telephone'],
					'status' => $student['status'],
				),
				'Course' => $student['Course']
			);

//pr($students);	// debug
//pr($students_period);	// debug

		unset($this->User->validate['email']);
		unset($this->User->validate['telephone']);
		unset($this->User->validate['status']);

		$this->User->saveAll($students, array('validate' => 'first', 'atomic'=>false));
		// xxx: manage Cake warning when repeating values (instead of ob_ trick)?
ob_start();
		$this->User->PeriodsStudent->saveAll($students_period, array('validate' => 'first', 'atomic'=>false));
$output = ob_get_clean();
//echo $output;	// debug

//pr($this->User->invalidFields());	// debug
//pr($this->User->PeriodsStudent->invalidFields());	// debug

		$this->Session->setFlash('Data uploaded!', null, array(), 'success');
		$this->redirect('/admin/students');
	}
}
?>
