<?php
/**
 * Form to upload the PermaMed import files
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 3-jan-2018
 * @license AGPL-3.0
 *
 */
use CRM_Sync_ExtensionUtil as E;

class CRM_Sync_Form_PermamedUpload extends CRM_Core_Form {
  public function buildQuickForm()
  {

      $maxFileSize = Civi::settings()->get('maxFileSize');
      $uploadFileSize = CRM_Utils_Number::formatUnitSize($maxFileSize . 'm', TRUE);
      //Fetch uploadFileSize from php_ini when $config->maxFileSize is set to "no limit".
      if (empty($uploadFileSize)) {
          $uploadFileSize = CRM_Utils_Number::formatUnitSize(ini_get('upload_max_filesize'), TRUE);
      }
      $uploadSize = round(($uploadFileSize / (1024 * 1024)), 2);
      $this->assign('uploadSize', $uploadSize);
      $this->add('File', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=255', TRUE);
      $this->setMaxFileSize($uploadFileSize);
      $this->addRule('uploadFile', ts('File size should be less than %1 MBytes (%2 bytes)', array(
          1 => $uploadSize,
          2 => $uploadFileSize,
      )), 'maxfilesize', $uploadFileSize);
      // $this->addRule('uploadFile', ts('Input file must be in CSV format'), 'utf8File');
      // $this->addRule('uploadFile', ts('A valid file must be uploaded.'), 'uploadedfile');
      $this->addButtons(array(
          array(
              'type' => 'submit',
              'name' => E::ts('Submit'),
              'isDefault' => TRUE,
          ),
      ));
      parent::buildQuickForm();
  }

    public function preProcess()
    {
        if (isset($this->_submitFiles['uploadFile'])) {
            $uploadFile = $this->_submitFiles['uploadFile'];
            $importer = new CRM_Sync_PermamedImporter();
            $importer ->truncate();
            $importer -> importCVStoTable($uploadFile['tmp_name']);
        }
    }

  public function postProcess() {
    // add processing of the file
    parent::postProcess();
  }

}
