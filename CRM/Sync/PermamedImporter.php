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

    const
        NUM_ROWS_TO_INSERT = 100;
    /**
     * CRM_Sync_PermamedImporter constructor.
     */
    public function __construct($fieldseperator = ',')
    {
        $this->fieldseperator=$fieldseperator;
    }

    public function truncate()
    {
        $dao = new CRM_Core_DAO();
        $dao->query('truncate table import_permamed');

    }

    public function importCVStoTable($file){
        // allow the file import to finish in 5 minutes
        ini_set('max_execution_time', 300);


        // now in code - but mebbe json it
        $mapping = array (
          'naam' => 2,
          'voornaam' => 3,
          'riziv' => 19,
          'geslacht' => 30
        );

        $sqlfields = array_keys($mapping);


        $fd = fopen($file, 'r');
        fgetcsv($fd, 0, $this->fieldseperator);
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