<h2>Verify Your Contact Information</h2>

<div class="message">You haven't verified your contact information for this school term.</div>

<b>&nbsp;Please review your contact information below.</b>


<?=$form->create(array('url'=>'/users/verify_contact'))?>

<?=$form->input('User.email', array('label'=>'Email * '))?>
<?=$form->input('User.telephone')?>

<?=$form->end('Continue')?>



<?//pr($this->data)?>