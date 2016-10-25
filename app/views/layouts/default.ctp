<?php
/* SVN FILE: $Id: default.ctp 7945 2008-12-19 02:16:01Z gwoo $ */
/**
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
 * @subpackage    cake.cake.console.libs.templates.skel.views.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="width: 100%; height: 100%;">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('NYUS Private Lesson Scheduling:'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $html->meta('icon');

		echo $html->css('nyu');
		echo $html->css('scheduling');

		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
		<div id="header"> </div>
		
		<div id="content">
			<div id="title">
				<?php if($session->check('User.id')) echo $this->element('menu'); ?>
				<h1><?php echo $html->link(__('Scheduling System', true), '/'); ?></h1>
			</div>

<?php if($session->check('User.name')): ?>
			<div class="right" style="margin: -.5em 1em 0 0; background: #fff;">
				<b>Welcome, <?=$session->read('User.name')?></b>
			</div>
<?php endif; ?>

			<?php if($session->check('Message.error')): ?><div class="error"><?php $session->flash('error'); ?></div><?php endif; ?>
			<?php if($session->check('Message.warning')): ?><div class="warning"><?php $session->flash('warning'); ?></div><?php endif; ?>
			<?php if($session->check('Message.success')): ?><div class="success"><?php $session->flash('success'); ?></div><?php endif; ?>
			<?php $session->flash(); ?>

			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
      &copy; <?=date('Y')?>
      <?=$html->link('New York University', 'http://www.nyu.edu')?>.
      <?=$html->link('Steinhardt School of Culture, Education, and Human Development', 'http://steinhardt.nyu.edu')?>
			<?php /* echo $html->link(
					$html->image('cake.power.gif', array('alt'=> __("CakePHP: the rapid development php framework", true), 'border'=>"0")),
					'http://www.cakephp.org/',
					array('target'=>'_blank'), null, false
				);
			*/?>
		</div>
	</div>
	<?php echo $cakeDebug; ?>
</body>
</html>