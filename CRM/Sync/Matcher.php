<?php
/**
 * Identifies a praktijk
 * 1) relationship fields
 * 2) address fields
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 4-jan-2018
 * @license AGPL-3.0
 */

class CRM_Sync_Matcher {

  private $_context;

  /**
   * CRM_Sync_PraktijkMatcher constructor.
   */
  public function __construct($context) {
    $this->_context = $context;
  }


  /**
   * @param $street
   * @param $city
   *
   * @return null|string
   */
  public function matchPraktijk($street, $city) {

    $praktijk_id = CRM_Core_DAO::singleValueQuery(
      "SELECT contact_id_b FROM civicrm_relationship rel
       JOIN   civicrm_contact c ON (rel.contact_id_b = c.id AND c.is_deleted=0)
       WHERE relationship_type_id = %2 AND contact_id_a =%1", array(
        1 => array($this->_context['contact_id'], 'Integer'),
        2 => array($this->_context['relationship_type_id'], 'Integer'),
      )
    );

    if (!isset($praktijk_id)) {
      $praktijk_id = CRM_Core_DAO::singleValueQuery("
      SELECT c.id FROM civicrm_contact c
      JOIN civicrm_address adr ON (adr.contact_id = c.id)
      JOIN civicrm_location_type loc ON (loc.id = adr.location_type_id AND loc.name='Praktijkadres')
      WHERE adr.street_address = %1 AND adr.city = %2 and c.is_deleted=0 and c.contact_type='Organization'
      ", array(
          1 => array($street, 'String'),
          2 => array($city, 'String'),
        )
      );
    }

    return $praktijk_id;
  }

  /**
   * @param $praktijkopleider
   * @param $warnings
   *
   * @return bool
   */
  public function matchPraktijkOpleider($praktijkopleider,&$warnings) {

    if (empty($praktijkopleider)) {
      return FALSE;
    }

    $dao = CRM_Core_DAO::executeQuery("
     SELECT id FROM civicrm_contact
     WHERE  concat(last_name,', ',first_name) = %1 AND contact_type='Individual'", array(
        1 => array($praktijkopleider, 'String'),
      )
    );

    $dao->fetch();

    if($dao->N==0){
      return false;
    } else {
      if($dao->N>1){
        $warnings[] = 'meer mogenlijkheden gevonden voor praktijkopleider '.$praktijkopleider. ' een willekeurige gekozen';
      }
      return $dao->id;
    }
  }
}