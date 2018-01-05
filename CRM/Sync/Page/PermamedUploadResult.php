<?php
use CRM_Sync_ExtensionUtil as E;

class CRM_Sync_Page_PermamedUploadResult extends CRM_Core_Page {

  private function failures(){
    $failures = array();
    $dao = CRM_Core_DAO::executeQuery("SELECT * FROM import_permamed WHERE processed = 'F'");
    while($dao->fetch()){
      $row = array(
        'id' => $dao->id,
        'message' => $dao ->message,
      );
      $failures[]=$row;
    }
    return $failures;
  }

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Result of the upload of the Permamed file'));
    $this->assign('failures',$this->failures());
    parent::run();
  }

}
