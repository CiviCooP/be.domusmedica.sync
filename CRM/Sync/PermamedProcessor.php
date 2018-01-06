<?php

/**
 * Process the permaned table with the civicrm_api3
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 4-jan-2018
 * @license AGPL-3.0
 *
 */
class CRM_Sync_PermamedProcessor {

  private $stepsize;

  /**
   * CRM_Sync_PermamedProcessor constructor.
   *
   * @param $stepsize
   */
  public function __construct($stepsize = 10) {
    $this->stepsize = $stepsize;
  }

  private function calcSteps() {
    $calcRows = CRM_Core_DAO::singleValueQuery('SELECT count(1) FROM import_permamed WHERE processed = %1', array(
      '1' => array('N', 'String'),
    ));
    return ceil($calcRows / $this->stepsize);
  }


  public function process(CRM_Queue_TaskContext $ctx) {
    $dao = CRM_Core_DAO::executeQuery('SELECT * FROM import_permamed WHERE processed = %1 LIMIT %2', array(
      '1' => array('N', 'String'),
      '2' => array($this->stepsize, 'Integer'),
    ));
    try {
      while ($dao->fetch()) {
        $this->processRecord($dao);
      }
    } catch (Exception $ex) {
      Civi::log()->info($ex);
    }
    return TRUE;
  }

  public function fillQueue($queue) {
    $calcSteps = $this->calcSteps();
    for ($i = 0; $i <= $calcSteps; $i++) {
      $task = new CRM_Queue_Task(
        array(
          $this,
          'process',
        ), //call back method
        array(), //parameters,
        "Processed " . $i * $this->stepsize . " rows"
      );
      $queue->createItem($task);
    }
  }

  /**
   * @param $dao
   */
  private function processRecord($dao) {

    /* the array errors is used by the processing
       functions to store errors so they can be
       examined later. Now all the processing functions skip
       processing if they find and error.
       - todo possible make a difference errors and warnings
         warnings do not skip.
    */
    $errors = array();
    /* context is used to pass technical keys from on
       processing function to another. At the moment two
       keys are passed
       - contact_id  id of the arts
       - praktijk_id is (id of the connected organization
    */
    $context = array();

    try {

      /* processing functions have all the same structure
         - check for errors - if so skip
         - check if the field that must me updated is in
           the input - if so skip
         - look if the object to be created already exists
           finds its technical id.
         - create or update the object (using the api and its id)
         - fill the context if needed
         - fill the errors
         - return

         however there are differences how the functions map
         the import table fields to the api arguments

         1) a specialist functions does the mapping inside the
            function (such a function is used one time e.g
            procesPraktijk.
         2) a generic function does the mapping outside the function
            (below, example processEmail)
      */

      $this->processCheck($dao, $errors);
      $this->processContact($dao, $errors, $context);
      $this->processAddress($errors, array(
        'contact_id' => $context['contact_id'],
        'location_type_id' => 'Prive',
        'street_address' => $dao->straat_prive . ' ' . $dao->huisnummer_prive,
        'city' => $dao->stad_prive,
        'postal_code' => $dao->postcode_prive,
      ));
      $this->processEmail($errors, array(
        'contact_id' => $context['contact_id'],
        'location_type_id' => 'Prive',
        'email' => $dao->email_prive,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['contact_id'],
        'location_type_id' => 'Prive',
        'phone_type_id' => 'Phone',
        'phone' => $dao->telefoon_prive,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['contact_id'],
        'location_type_id' => 'Prive',
        'phone_type_id' => 'Mobile',
        'phone' => $dao->gsm_prive,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['contact_id'],
        'location_type_id' => 'Prive',
        'phone_type_id' => 'Fax',
        'phone' => $dao->fax_prive,
      ));
      $this->processPraktijk($dao,$errors,$context);
      $this->processAddress($errors, array(
        'contact_id' => $context['praktijk_id'],
        'location_type_id' => 'Praktijkadres',
        'street_address' => $dao->straat . ' ' . $dao->huisnummer,
        'city' => $dao->stad,
        'postal_code' => $dao->postcode,
      ));
      $this->processEmail($errors, array(
        'contact_id' => $context['praktijk_id'],
        'location_type_id' => 'Praktijkadres',
        'email' => $dao->email,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['praktijk_id'],
        'location_type_id' => 'Praktijkadres',
        'phone_type_id' => 'Phone',
        'phone' => $dao->telefoon,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['praktijk_id'],
        'location_type_id' => 'Praktijkadres',
        'phone_type_id' => 'Mobile',
        'phone' => $dao->gsm,
      ));
      $this->processPhone($errors, array(
        'contact_id' => $context['praktijk_id'],
        'location_type_id' => 'Praktijkadres',
        'phone_type_id' => 'Fax',
        'phone' => $dao->fax,
      ));
    } catch (Exception $ex) {
      $errors[] = $ex;
    }
    if (empty($errors)) {
      CRM_Core_DAO::executeQuery('UPDATE import_permamed SET processed = %2 WHERE id=%1', array(
        1 => array($dao->id, 'Integer'),
        2 => array('S', 'String'),
      ));
    }
    else {
      $message = implode($errors, ';');
      CRM_Core_DAO::executeQuery('UPDATE import_permamed SET processed = %2, message=%3 WHERE id=%1', array(
        1 => array($dao->id, 'Integer'),
        2 => array('F', 'String'),
        3 => array($message, 'String'),
      ));
    }


  }

  /**
   * @param $dao
   * @param $errors
   */
  private function processCheck($dao, &$errors) {
    if (!empty($errors)) {
      return;
    }

    if ($dao->riziv == 0) {
      $errors[] = 'A permaned record with a riziv of 0 is skipped';
      return;
    }

    if ($dao->haio) {
      $errors[] = 'Haios are not processed';
      return;
    }
  }

  /**
   * @param $dao
   * @param $errors
   * @param $context adds contact_id of the found or the inserted contact
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function processContact($dao, &$errors, &$context) {
    if (!empty($errors)) {
      return;
    }

    $config = CRM_Sync_Config::singleton();

    $contact_id = CRM_Core_DAO::singleValueQuery("SELECT id FROM civicrm_contact WHERE external_identifier = %1", array(
      1 => array($dao->riziv, 'Integer'),
    ));

    $apiParams = array();
    if ($contact_id) {
      $apiParams['id'] = $contact_id;
    }

    $apiParams['contact_type'] = 'Individual';
    $apiParams['contact_sub_type'] = 'Arts';
    $apiParams['external_identifier'] = $dao->riziv;
    $apiParams['first_name'] = $dao->voornaam;
    $apiParams['last_name'] = $dao->naam;
    $apiParams['custom_' . $config->getBankrekeningCustomFieldId()] = $dao->rekeningnummer_prive;
    $apiParams['custom_' . $config->getActiefvoorWachtdienstCustomFieldId()] = $dao->actief_voor_wachtdienst;

    $result = civicrm_api3('Contact', 'create', $apiParams);

    if ($result['is_error']) {
      $errors[] = $result['error_message'];
    }

    $context['contact_id'] = $result['id'];

  }

  /**
   * @param $dao
   * @param $errors
   * @param $context
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function processPraktijk($dao, &$errors, &$context) {
    if (!empty($errors)) {
      return;
    }

    $config = CRM_Sync_Config::singleton();

    $praktijk_id = CRM_Core_DAO::singleValueQuery(
      "SELECT contact_id_b FROM civicrm_relationship rel
       WHERE relationship_type_id = %2 AND contact_id_a =%1",array(
         1 => array($context['contact_id'],'Integer'),
         2 => array($config->getLidGroepsPraktijkRelationShipId(),'Integer')
      )
    );

    $apiParams = array();
    if ($praktijk_id) {
      $apiParams['id'] = $praktijk_id;
    }

    $praktijknaam = !isset($dao->praktijknaam)?$dao->praktijknaam:'Huisartsenpraktijk '.$dao->naam;

    $apiParams['contact_type'] = 'Organization';
    $apiParams['contact_sub_type'] = 'Praktijk';
    $apiParams['organization_name'] = $praktijknaam;
    $apiParams['custom_' . $config->getBankrekeningCustomFieldId()] = $dao->rekeningnummer_prive;

    $result = civicrm_api3('Contact', 'create', $apiParams);

    if ($result['is_error']) {
      $errors[] = $result['error_message'];
      return;
    } else {
      $context['praktijk_id'] = $result['id'];
    }

    if(!isset($praktijk_id)){
      $result = civicrm_api3('Relationship','create',array(
        'contact_id_a' => $context['contact_id'],
        'contact_id_b' => $context['praktijk_id'],
        'relationship_type_id' => $config->getLidGroepsPraktijkRelationShipId(),
      ));

      if ($result['is_error']) {
        $errors[] = $result['error_message'];
      }
    }
  }

  /**
   * @param $errors
   * @param $apiParams
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function processAddress(&$errors, $apiParams) {
    if (!empty($errors)) {
      return;
    }

    $address_id = CRM_Core_DAO::singleValueQuery(
      "SELECT adr.id FROM civicrm_address adr
       JOIN civicrm_location_type loc ON (loc.id = adr.location_type_id)
       WHERE adr.contact_id=%1 AND loc.name = %2 ", array(
        1 => array($apiParams['contact_id'], 'Integer'),
        2 => array($apiParams['location_type_id'], 'String'),
      )
    );

    if ($address_id) {
      $apiParams['id'] = $address_id;
    }

    $result = civicrm_api3('Address', 'create', $apiParams);

    if ($result['is_error']) {
      $errors[] = $result['error_message'];
    }
  }

  /**
   * @param $errors
   * @param $apiParams
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function processEmail(&$errors, $apiParams)
  {
    if (!empty($errors)) {
      return;
    }

    if (empty($apiParams['email'])) {
      return;
    }

    $email_id = CRM_Core_DAO::singleValueQuery("
      SELECT em.id FROM civicrm_email em 
      JOIN civicrm_location_type loc ON (em.location_type_id = loc.id)
      WHERE em.contact_id = %1 AND loc.name = %2", array(
      1 => array($apiParams['contact_id'], 'Integer'),
      2 => array($apiParams['location_type_id'], 'String'),
    ));

    if ($email_id) {
      $apiParams['id'] = $email_id;
    }

    $result = civicrm_api3('Email', 'create', $apiParams);

    if ($result['is_error']) {
      $errors[] = $result['error_message'];
    }

  }

  /**
   * @param $errors
   * @param $apiParams
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function processPhone(&$errors, $apiParams)
  {
    if (!empty($errors)) {
      return;
    }

    if (empty($apiParams['phone'])) {
      return;
    }

    $phone_id = CRM_Core_DAO::singleValueQuery("
      SELECT ph.id FROM civicrm_phone ph
      JOIN civicrm_location_type loc ON (ph.location_type_id = loc.id)
      JOIN civicrm_option_value ov ON (ov.value = phone_type_id)
      JOIN civicrm_option_group g ON (ov.option_group_id = g.id and g.name='phone_type')
      WHERE ph.contact_id = %1 AND loc.name = %2 AND ov.name=%3", array(
      1 => array($apiParams['contact_id'], 'Integer'),
      2 => array($apiParams['location_type_id'], 'String'),
      3 => array($apiParams['phone_type_id'] , 'String')
    ));

    if ($phone_id) {
      $apiParams['id'] = $phone_id;
    }

    $result = civicrm_api3('Phone', 'create', $apiParams);

    if ($result['is_error']) {
      $errors[] = $result['error_message'];
    }

  }

}