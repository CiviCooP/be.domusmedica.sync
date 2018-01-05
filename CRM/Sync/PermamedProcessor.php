<?php

/**
 * Imports the CVS permamed file into the temporary import table
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
        "Processed " . $i*$this->stepsize . " rows"
      );
      $queue->createItem($task);
    }
  }

  private function processRecord($dao) {
    echo "$dao->id  \n";
    $errors = array();
    try {
      $this->processContact($dao, $errors);
    } catch (Exception $ex) {
        $errors[] = $ex;
    }
    if(empty($errors)){
      CRM_Core_DAO::executeQuery('update import_permamed set processed = %2 where id=%1', array(
        1 => array($dao->id,'Integer'),
        2 => array('S','String')
      ));
    } else {
      $message = implode($errors,';');
      CRM_Core_DAO::executeQuery('update import_permamed set processed = %2, message=%3 where id=%1', array(
        1 => array($dao->id,'Integer'),
        2 => array('F','String'),
        3 => array($message,'String')
      ));
    }


  }

  private function processContact($dao,&$errors){
     if($dao->riziv==0){
       $errors[] = 'A permaned record with a riziv of 0 is skipped';
     }
  }


}