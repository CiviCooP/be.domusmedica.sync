<?php

/**
 * Translates the gender from the Permamed Import to the Civi Standard.
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 4-jan-2018
 * @license AGPL-3.0
 *
 */
class CRM_Sync_GenderTranslator {

  /**
   * CRM_Sync_GenderTranslator constructor.
   */
  public function __construct() {
  }

  /**
   * @param $permamedGender
   *
   * @return bool|int
   */
  public function translate($permamedGender) {
    if (empty($permamedGender)) {
      return FALSE;
    }
    elseif ($permamedGender == 'vrouwelijk') {
      return 1;
    }
    elseif ($permamedGender == 'mannelijk') {
      return 2;
    }
    else {
      throw Exception("Unknown gender in import $permamedGender");
    }

  }
}