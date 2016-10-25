<?php
/* SVN FILE: $Id: app_controller.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Short description for class.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
class AppController extends Controller {
	
	/**
	 * CakePHP callback controlling user access.
	 * 
	 * @author Jorge Orpinel
	 */
	function beforeFilter () {
		
		// public access:
		if(!$this->Session->read('User.id')) {
			$access = false;
			
			$publicAccess = array(
				'/login',
				'/users/login',
				'/users/password',
			);
			
			foreach ($publicAccess as $url)
				if (ereg($url, $this->here)) { $access = true; break; }
			
			if(!$access) { $this->redirect('/login'); exit; } // security
		}
		
		// /admin* access:
		$adminAccess = array(
			'/admin',
		);
		
		foreach ($adminAccess as $url)
			if (ereg($url, $this->here) and (!$this->Session->check('User.type') or $this->Session->read('User.type') != 'admin'))
				{ $this->cakeError('error404'); exit; } // security
		
	}
	
	
	
	/** Auxiliaty method to send form data as 2nd url parameter to other urls
	 * @param String $controller controller to route to
	 * @param String $method method to route to. Send 'null' to jump it in te url formation
	 * @param1 String first url parameter (if not sent in the url, param2 will be the only one)
	 * NOTE: Send the POST parameters in data[Param][1] and [2]
	 */
	function route($controller=false, $method, $param1=false) {
		if(!$controller) $controller = $this->params['controller'];
		if(!$param1 and isset($this->data['Param'][1])) $param1 = $this->data['Param'][1];
		if(isset($this->data['Param'][2])) $param2 = $this->data['Param'][2];
		$this->redirect("/$controller".($method!='null'?"/$method":null).(isset($param1)?"/$param1":null).(isset($param2)?"/$param2":null));
	}
	
	
	
}
?>