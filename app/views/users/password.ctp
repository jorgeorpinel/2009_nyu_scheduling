<h3>Your New Password</h3>

<?if(isset($user)):?>

send email with info. ...

<?pr($user)	// xxx: remove when email sent ?>

<div class="mini message">Didn't recieve the email? Try again:</div>

<?endif; ?>

<div class="isolated" style="width: 400px;">
	
	<div class="mini warning" style="margin: 5px 0;">Please provide your NYU ID Number:</div>
	
<?$javascript->link('prototype.js', false); ?>
	<button class="right" onClick="javascript:window.location='<?=$html->url('/users/password')?>'+'/'+$('UserId').value">Get my password!</button>
	
	<?=$form->input('User.id', array('type'=>'text', 'label'=>'NYU ID Number '))?>
	
</div>