<?php
/**
 * Imports the CVS permamed file into the temporary import table
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 4-jan-2018
 * @license AGPL-3.0
 *
 */
class CRM_Sync_PermamedImporter
{

    private $fieldseperator;
    private $skip;

    const
        NUM_ROWS_TO_INSERT = 100;
    /**
     * CRM_Sync_PermamedImporter constructor.
     */
    public function __construct($fieldseperator = ',',$skip=5)
    {
        $this->fieldseperator=$fieldseperator;
        $this->skip = $skip;
    }

  /**
   *
   */
  public function truncate()
    {
        $dao = new CRM_Core_DAO();
        $dao->query('truncate table import_permamed');

    }

  /**
   * @param $file
   */
  public function importCVStoTable($file){
        // allow the file import to finish in 5 minutes
        ini_set('max_execution_time', 300);


        // now in code - but mebbe json it
        $mapping = array (
          'naam' => 0,
          'voornaam' => 1,
          'straat' => 2,
          'huisnummer' => 3,
          'postcode' => 4,
          'stad'     => 5,
          'telefoon' => 6,
          'gsm'      => 7,
          'email'    => 8,
          'fax' => 9,
          'praktijknaam' => 10,
          'website' => 12,
          'rekeningnummer' => 13,
          'riziv' => 14,
          'straat_prive' => 15,
          'huisnummer_prive' => 16,
          'postcode_prive' => 17,
          'stad_prive' => 18,
          'telefoon_prive' => 19,
          'gsm_prive' => 20,
          'email_prive' => 21,
          'rekeningnummer_prive' => 22,
          'fax_prive' => 23,
          'geslacht' => 24,
          'haio' => 32,
          'opleidingsjaar' => 33,
          'praktijk_opleider' => 34,
          'actief_voor_wachtdienst' => 36,
          'emd' => 39,
        );

        $sqlfields = array_keys($mapping);


        $fd = fopen($file, 'r');

        $csvrow = array();
        // skip header columns
        for($i=0;$i<$this->skip;$i++) {

            fgetcsv($fd, 0, $this->fieldseperator);

        }
        $dao = new CRM_Core_DAO();
        $sql = NULL;
        $first = TRUE;
        $count = 0;
        while ($csvrow = fgetcsv($fd, 0, $this->fieldseperator)) {
            $row = array();
            foreach($mapping as $pos){
                $row[] = $csvrow[$pos];
            }

            if (!$first) {
                $sql .= ', ';
            }
            else {
                $first = FALSE;
            }
            // trim whitespace
            $row = array_map(function ($string) {
                return trim($string, chr(0xC2) . chr(0xA0));
            }, $row);
            // add quotes
            $row = array_map(['CRM_Core_DAO', 'escapeString'], $row);
            $sql .= "('" . implode("', '", $row) . "')";
            $count++;
            if ($count >= self::NUM_ROWS_TO_INSERT && !empty($sql)) {
                $sql = "INSERT INTO import_permamed (" . implode(',', $sqlfields) . ") VALUES $sql";
                $dao->query($sql);
                $sql = NULL;
                $first = TRUE;
                $count = 0;
            }
        }
        if (!empty($sql)) {
            $sql = "INSERT INTO import_permamed (" . implode(',', $sqlfields) . ") VALUES $sql";;
            $dao->query($sql);
        }
        fclose($fd);

    }
}