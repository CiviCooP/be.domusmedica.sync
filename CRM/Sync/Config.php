<?php
/**
 * Singleton to store the technical ids of the custom fields
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 5-jan-2018
 * @license AGPL-3.0
 *
 */

class CRM_Sync_Config {

  private static $_singleton;

  private $_bankrekeningCustomFieldId;

  private $_opleidingsjaarCustomFieldId;

  private $_actiefvoorWachtdienstCustomFieldId;

  private $_emdCustomFieldId;

  private $_artsBijRelationShipId;

  private $_volgtopleidingInDePrakijkRelationShipId;

  private $_isHaioVanRelationShipId;

  private $_opleidingsJaarCustomFieldId;

  private $_permamedGroupId;

  /**
   * CRM_Sync_Config constructor.
   */
  public function __construct() {
    try {
      $this->_bankrekeningCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Bankrekening",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Custom Field Bankrekening not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_opleidingsjaarCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Opleidingsjaar",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Custom Field Opleidingsjaar not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_actiefvoorWachtdienstCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "vrijgesteld_van_wacht",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Custom Field vrijgesteld_van_wacht not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_emdCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "medisch_pakket",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Custom Field medisch_pakket not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_artsBijRelationShipId = civicrm_api3('RelationshipType', 'getvalue', array(
        'return' => "id",
        'name_a_b' => "is arts bij",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: RelationShip Type Is is arts bij not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_volgtopleidingInDePrakijkRelationShipId = civicrm_api3('RelationshipType', 'getvalue', array(
        'return' => "id",
        'label_a_b' => "volgt opleiding in de praktijk",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: RelationShip Type Is is volgt opleiding in de praktijk not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_isHaioVanRelationShipId = civicrm_api3('RelationshipType', 'getvalue', array(
        'return' => "id",
        'name_a_b' => "is Haio van",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: RelationShip Type Is is Haio van bij not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_opleidingsJaarCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Opleidingsjaar",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Custom Field Opleidingsjaar not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }
    try {
      $this->_permamedGroupId = civicrm_api3('Group', 'getvalue', array(
        'return' => "id",
        'title' => "Permamed",
      ));
    } catch (Exception $ex) {
      throw new Exception('Oops: Group Permamed not found in configuration (File ' . __FILE__ . ' on line ' . __LINE__ . ')');
    }

  }

  /**
   * @return mixed
   */
  public function getBankrekeningCustomFieldId() {
    return $this->_bankrekeningCustomFieldId;
  }

  /**
   * @return array
   */
  public function getArtsBijRelationShipId() {
    return $this->_artsBijRelationShipId;
  }

  /**
   * @return array
   */
  public function getVolgtopleidingInDePrakijkRelationShipId() {
    return $this->_volgtopleidingInDePrakijkRelationShipId;
  }

  /**
   * @return mixed
   */
  public function getOpleidingsjaarCustomFieldId() {
    return $this->_opleidingsjaarCustomFieldId;
  }

  /**
   * @return mixed
   */
  public function getActiefvoorWachtdienstCustomFieldId() {
    return $this->_actiefvoorWachtdienstCustomFieldId;
  }

  /**
   * @return array
   */
  public function getIsHaioVanRelationShipId() {
    return $this->_isHaioVanRelationShipId;
  }



  /**
   * @return mixed
   */
  public function getEmdCustomFieldId() {
    return $this->_emdCustomFieldId;
  }

  /**
   * @return array
   */
  public function getPermamedGroupId() {
    return $this->_permamedGroupId;
  }


  /**
   * Instantiates the singleton
   *
   * @return CRM_Sync_Config
   * @access public
   * @static
   */
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Sync_Config();
    }
    return self::$_singleton;
  }

}