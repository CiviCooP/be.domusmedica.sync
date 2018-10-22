<?php
/**
 *  @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 10/22/18 5:29 PM
 *  @license AGPL-3.0
 */

use CRM_Sync_ExtensionUtil as E;

class CRM_Sync_Page_PermamedUploadResult extends CRM_Core_Page {

  /**
   * @return array
   */
  private function failures(){
    $failures = array();
    $dao = CRM_Core_DAO::executeQuery("
       SELECT imp.id, imp.naam, imp.message ,c.id contact_id FROM import_permamed imp
       left join civicrm_contact c on (c.external_identifier=riziv)
       WHERE processed = 'F'");
    while($dao->fetch()){



      $row = array(
        'id' => $dao->id,
        'contact_id' => $dao->contact_id,
        'naam' => $dao->naam,
        'message' => $dao ->message,
      );
      $failures[]=$row;
    }
    return $failures;
  }

  /**
   * @return null|void
   */
  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Result of the upload of the Permamed file'));
    $this->assign('failures',$this->failures());
    parent::run();
  }

}
