<?php

namespace Nottingham\Locking;

class Locking extends \ExternalModules\AbstractExternalModule {


    function redcap_data_entry_form( $project_id, $record, $instrument, $event_id, $group_id, $repeat_instance )
    {
        // if there are data quality rule errors, then pop a message to notify user form has been reset to incomplete
        if(isset($_SESSION['module_locking_dq_errors']))
        { 
            unset($_SESSION['module_locking_dq_errors']);
            $this->alert("The form has been reset to incomplete as there are data quality violations. \\nPlease fix data or exclude violation and reset form to complete");
        }
     
    }

    function redcap_save_record( $project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {

        $form_events = $this->getProjectSetting('form-event-name', $project_id);
        $forms = $this->getProjectSetting('form-name', $project_id);
        global $Proj;

        // Get all configured instrument that have locking requests when form is saved
        for ($i = 0; $i < count($form_events); $i++)
        {
            
             if ($form_events[$i] === $event_id && $forms[$i] === $instrument) {

               
		$data = \REDCap::getData( 'array', $record, $instrument . '_complete', $event_id) ;
                
                $repeat_instrument = $Proj->isRepeatingForm($event_id, $instrument) ? $instrument : "";
                $dq = new \DataQuality();
                list ($dq_errors, $dq_errors_excluded) = $dq->checkViolationsSingleRecord($record, $event_id, $instrument, array(), $repeat_instance, $repeat_instrument);

                // Check that the submitted form is complete (<instrument_name>_complete == 2) and there are no data quality errors 
                if($data[$record][$event_id][$instrument . '_complete'] == '2')
                {
                    if(empty($dq_errors))
                    {
                        
                        $lock_form_events = $this->getProjectSetting('lock-form-event-name', $project_id);
                        $lock_forms = $this->getProjectSetting('lock-form-name', $project_id);

                        // lock all the forms that requested to lock
                        for ($lock = 0; $lock < count($lock_form_events[$i]); $lock++)
                        {
                            $sql = "insert into redcap_locking_data (project_id, record, event_id, form_name, username, timestamp, instance)
                                                values ($project_id, '" . $record . "', " . $lock_form_events[$i][$lock] . ",
                                                '" . $lock_forms[$i][$lock]. "', '" . USERID . "', '".NOW."', " . checkNull($repeat_instance) . ")";
                            $description = "Action: Locking Instrument\nEvent: ".$lock_form_events[$i][$lock]. "\nInstrument: " .$lock_forms[$i][$lock]. "\nSaving Event - ".$form_events[$i]. "\nSaving instrument: " .$forms[$i];


                             if (db_query($sql))
                            {
                                \REDCap::logEvent("Locking Instrument", $description, "LOCK_RECORD", $record, "",  $project_id);
                            }

                        }
                    }  
                    
                    else
                    {
                        // as there are data quality rule errors, set form to incomplete
                        $inputData[$record][$event_id][$instrument . '_complete'] = 0;
                        $result = \REDCap::saveData( 'array', $inputData, 'normal', 'YMD', 'flat', null, true );
                        // set session varible to pop-up message box to inform user
                        $_SESSION['module_locking_dq_errors'] = implode(",", array_merge($dq_errors, $dq_errors_excluded));
  
                    }
                }
                
            }
        }
    }
    
     public function alert($msg) {
         echo "<script type='text/javascript'>alert('$msg');</script>";
  
    }
        

    

  

}
