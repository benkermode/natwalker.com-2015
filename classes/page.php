<?

class page  {

  private $sections = array();
  private $form = array();
  private $tpl_content;
  private $fdata;
  private $gf;

  public function __construct ( $sections ) {
    $this->sections = $sections;
    $this->gf = new genFuncs( $this->sections );
  }

  public function layoutPage ()
  {
 
    // $this->form = new formHandler();

    // $this->fdata = $this->form->getFormData();

 
    // $this->tpl_content = ( !$this->fdata ) ? $this->getTemplate('') : $this->getTemplate( 'form' ); 
    $global_template = 'index';
    if ( $this->sections['section'] == 'cms' )
      {
        $global_template = 'cms';
      }

    echo ( $this->getTemplate ( $global_template ) );
    include ( $this->getTemplate ( $global_template ) );

  }

  private function getTemplate ( $which ) 
  {
    $which = ( $which ) ? $which : $this->gf->getSectionsString();
    $file = T_TEMPLATES_DIR . '/' . $which . '.html';
    return $file;
  }
}

?>