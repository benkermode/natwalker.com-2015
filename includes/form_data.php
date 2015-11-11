<?
$this->fdata = array();
$this->fdata['login'] = array();

$this->fdata['login']['type'] = 'login';
$this->fdata['login']['float'] = 'left';

$this->fdata['login']['elements'] = array ();
$this->fdata['login']['elements']['email'] = array ('type'=>'email','mand'=>true,'label'=>'Email');
$this->fdata['login']['elements']['password'] = array ('type'=>'password','mand'=>true);


$this->fdata['contact'] = array();
$this->fdata['contact']['recipient'] = ADMIN_EMAIL;
$this->fdata['contact']['subject'] = CONTACT_SUBJECT;
$this->fdata['contact']['adminEmail'] = ADMIN_EMAIL;
$this->fdata['contact']['float'] = 'left';
        
$this->fdata['contact']['elements'] = array ();
$this->fdata['contact']['elements']['name_first'] = array ('type'=>'text','mand'=>true,'label'=>'First Name');
$this->fdata['contact']['elements']['name_last'] = array ('type'=>'text','mand'=>true,'label'=>'Last Name');
$this->fdata['contact']['elements']['email'] = array ('type'=>'email','mand'=>true);
$this->fdata['contact']['elements']['phone'] = array ('type'=>'text','mand'=>false);
$this->fdata['contact']['elements']['message'] = array ('type'=>'textarea','mand'=>true);

?>