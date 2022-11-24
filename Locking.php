<?php

namespace Nottingham\Locking;



class Locking extends \ExternalModules\AbstractExternalModule {
    
    function redcap_every_page_before_render($project_id)
    {

      if ( ! empty( $_POST ) && $_POST['submit-action'] !== 'submit-btn-cancel' && substr( PAGE_FULL, strlen( APP_PATH_WEBROOT ), 19 ) == 'DataEntry/index.php' )
      {
            $locked = $this->isHardLocked($_GET['event_id'], $_GET['page']);
           
            if($locked)
            {
                $it_inbox = 'ms-nctu-it@exmail.nottingham.ac.uk';
                global $project_contact_email;
                if($project_contact_email != "")
                {
                    $it_inbox = $project_contact_email;
                }
                            
                \REDCap::email($it_inbox, $it_inbox, 
                                   "HARD LOCK VIOLATION ON ".\REDCap::getProjectTitle(), "Data cannot be updated as form has a hard lock - Project=".\REDCap::getProjectTitle()."(".$project_id.") User=".$GLOBALS['userid']." Record Id=".$_POST[\REDCap::getRecordIdField()]." Form=".$_GET['page']." Event Id=".$_GET['event_id']." Instance=".$_GET['instance'] );

                echo "This data cannot be updated as the data on this form <b>HARD</b> locked.<br>";
                echo " <a href='".PAGE_FULL."?pid=".$project_id."&page=".$_GET['page']."&event_id=".$_GET['event_id']."&id=".$_POST[\REDCap::getRecordIdField()]."&instance=".htmlspecialchars($_GET['instance'], ENT_QUOTES )."'>Return to form</a>";
                $this->exitAfterHook();
            }
        }
    }
    
    function redcap_every_page_top( $project_id )
    {

        // Stop here if not in a project or not on a data entry page.
        if ( $project_id === null ||
             substr( PAGE_FULL, strlen( APP_PATH_WEBROOT ), 10 ) != 'DataEntry/' )
        { 
                return;
        }
           
        $hard_lock = $this->getProjectSetting('full-lock', $project_id);
        $override = $this->getProjectSetting('override', $project_id);
        $intermediate_lock = $this->getProjectSetting('intermediate-lock', $project_id);
       
        $locked = false;
      
        global $Proj;
        global $lang;
        addLangToJS(array('data_entry_46', 'data_entry_442', 'data_entry_443'));
      
        $override_on = false;
        try
        {
            $override_on = $override && $this->framework->getUser()->isSuperUser();
        }
        catch(\Exception $ex)
        {
            // username not detected, therefore do not set override
        }
        
        $lockText = $intermediate_lock && !$hard_lock ? "Intermediate hard lock on" : "Full Hard Lock On";
        $locked=false;
        if(!$override_on && ($hard_lock || $intermediate_lock))
        {
             $locked=true;
        }
        else if($hard_lock || $intermediate_lock)
        {
            $lockText .= " - Override On";
            $locked = true;
        }
        
        if($locked)
        {
        
            echo   '<script type="text/javascript">   
                    // Single global scope object containing all variables/functions
                    var LockInfo = {};
                    LockInfo.setInfoText = function(txt) {
                        LockInfo.textvar = txt;
                    };
 
                    LockInfo.getInfoText = function() {
                    return this.textvar;
                    };LockInfo.setInfoText("'.$lockText.'");
                                       </script>';   
            if((str_contains(PAGE_FULL, "record_status_dashboard.php") || str_contains(PAGE_FULL, "record_home.php")))
            {
                 echo   '<script type="text/javascript">' . 
                                           $this->loadFile("LockingButton.js") .'LockInfo.setInfoText("'.$lockText.'");
                                       </script>';   
            }
                                       
        }
        
    }


    function redcap_data_entry_form( $project_id, $record, $instrument, $event_id, $group_id,  $repeat_instance)
    {
        // add delay so all the modules are comleted, so buttons can be removed after they created
        $this->delayModuleExecution();
          // if there are data quality rule errors, then pop a message to notify user form has been reset to incomplete
        if(isset($_SESSION['module_locking_dq_errors']))
        { 
            unset($_SESSION['module_locking_dq_errors']);
            $this->alert("The form has been reset to incomplete as there are data quality violations. \\nPlease fix data or exclude violation and reset form to complete");
        }
 
        $locked = $this->isHardLocked($event_id, $instrument);
      
        global $Proj;
        global $lang;
        addLangToJS(array('data_entry_182', 'data_entry_210', 'data_entry_212', 'data_entry_215', 'data_entry_234', 'data_entry_275', 'data_entry_275', 'data_entry_276', 'data_entry_287', 'data_entry_288', 'data_entry_289', 'data_entry_292', 'data_entry_409', 'data_entry_410', 'emlang_minimization_rando', 'form_renderer_24'));
      
        if($locked)
        {
            if($this->isFormLocked($project_id, $record, $event_id, $instrument, $repeat_instance))
            {
                echo   '<script type="text/javascript">'. $this->loadFile("LockingButton.js") .'removeLockControl();</script>';
            }
            else
            {
            
                echo   '<script type="text/javascript">'. $this->loadFile("LockingButton.js") .'removeSaveControl();</script>';              
            }              
        }
       
        
        if(!$override_on && !$locked)
        {
            $form_events = $this->getProjectSetting('form-event-name', $project_id);
            $forms = $this->getProjectSetting('form-name', $project_id);
            // note readonly field has been removed from configuration as not been implemented in redcap_every_page_before_render or cron job
            // as following discussions with data management team it was not required therefore would need addining back in configuration
            // and implmenting in redcap_every_page_before_render and cron job if required!
            $readonly = $this->getProjectSetting('readonly', $project_id);
            $lock_instances = $this->getProjectSetting('lock-new-instances', $project_id);
          
            // Get all configured instrument that have locking requests when form is saved
            if($form_events !== null)
            {
                for ($i = 0; $i < count($form_events); $i++)
                {

                    $lock_form_events = $this->getProjectSetting('lock-form-event-name', $project_id);
                    $lock_forms = $this->getProjectSetting('lock-form-name', $project_id);
                    $lock_form_type = $this->getProjectSetting('part-config-type', $project_id);

                    // lock all the forms that requested to lock
                    if($lock_form_events !== null)
                    {
                        for ($lock = 0; $lock < count($lock_form_events[$i]); $lock++)
                        {
                           if ($lock_form_events[$i][$lock]=== $event_id)
                           {

                                $lock_form = false;
                                if($lock_form_type[$i][$lock] === "2")
                                {
                                     $lock_form = true;

                                }


                                for ( $k = 0; $k < count( $lock_forms[$i][$lock] ); $k++ )
                                {
                                    if($lock_forms[$i][$lock][$k] === $instrument)
                                    {
                                        $lock_form = !$lock_form;
                                        break;
                                    }
                                }


                                //  $this->alert("search through forms" . $event_id .' '. $instrument. ' locking event='. $lock_form_events[$i][$lock]. ' form='.$lock_forms[$i][$lock]);
                                  if ($lock_form) {

                                      $isParentRepeating = $Proj->isRepeatingForm($form_events[$i], $forms[$i]) || $Proj->isRepeatingEvent($form_events[$i]) ;
                                      // form is locked and parent form is locked, and should be readonly therefore remove the lock button  
                                      if($this->isFormLocked($project_id, $record, $form_events[$i], $forms[$i], $repeat_instance, $isParentRepeating))
                                      {     
                                            if($readonly[$i])
                                            { 
                                                if($this->isFormLocked($project_id, $record, $event_id, $instrument, $repeat_instance))
                                                {
                                                    echo '<script type="text/javascript">'. $this->loadFile("LockingButton.js") .'removeLockControl();</script>';
                                                }
                                                else
                                                {
                                                    echo '<script type="text/javascript">'. $this->loadFile("LockingButton.js") .'removeSaveControl();</script>';
                                                }
                                            }
                                            else if($lock_instances[$i] && !$this->framework->getUser()->isSuperUser())
                                            {
                                                $isRepeating = $Proj->isRepeatingForm($event_id, $instrument) || $Proj->isRepeatingEvent($event_id) ;

                                                // if repeating event or repeating form, then make sure and not super user, make sure a new instance cannot be added
                                                if($isRepeating)
                                                {
                                                    list ($instanceTotal, $instanceMax) = \RepeatInstance:: getRepeatFormInstanceMaxCount($record, $event_id, $instrument, $Proj);
                                                    if((int)$repeat_instance > $instanceMax)
                                                    {

                                                        echo '<script type="text/javascript">'. $this->loadFile("LockingButton.js") .'removeSaveControl("New instance cannot be added,<br>please contact administrator.");</script>';
                                                    }
                                                }


                                            }
                                      }
                                }
                           }
                       }
                    }
                }
            }
        }
    }
    
  
    
    function validateSettings( $settings )
    {
         $errMsg = "";
        if($settings['configure-locking'] === true)
        {
            for ( $i = 0; $i < count( $settings['form-event-name'] ); $i++ )
            {
                if($settings['form-event-name'][$i] == '' || $settings['form-name'][$i] == '')
                {
                    $errMsg .= "\n- Saving instrument event or form " . ($i+1) . ": is missing";
                }
                
                for ( $j = 0; $j < count( $settings['lock-form-event-name'][$i] ); $j++ )
                {
                    
                    if($settings['part-config-type'][$i][$j] === "1")
                    {

                       for ( $k = 0; $k < count( $settings['lock-form-name'][$i][$j] ); $k++ )
                       {

                           if($settings['lock-form-name'][$i][$j][$k] == '')
                           {
                               $errMsg .= "\n- When instrument (Event=". $settings['form-event-name'][$i]. " Form=". $settings['form-name'][$i]. ") is saved, form ". ($k+1) ." to lock is missing";
                           } 
                       }
                    }
                }
            }
		
        }
        else
        {
            for ( $i = 0; $i < count( $settings['form-event-name'] ); $i++ )
            {
                if($settings['form-event-name'][$i] != '' || $settings['form-name'][$i] != '')
                {
                    $errMsg .= "\n- Saving instrument event or form " . ($i+1) . ": should not be defined as locking is not checked";
                }
            }
		
        }
        if($errMsg !== '')
        {
            return $errMsg;
        }
        return null;
    }
    
   
  
	
    function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
            $this-> redcap_data_entry_form( $project_id, $record, $instrument, $event_id, $group_id, $repeat_instance );

    }

    function redcap_save_record( $project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {

        $form_events = $this->getProjectSetting('form-event-name', $project_id);
        $forms = $this->getProjectSetting('form-name', $project_id);
        global $Proj;
        
        

        // Get all configured instrument that have locking requests when form is saved
        if($form_events !== null)
        {
            for ($i = 0; $i < count($form_events); $i++)
            {
                 if ($form_events[$i] === $event_id && $forms[$i] === $instrument) {


                    $data = \REDCap::getData( 'array', $record, $instrument . '_complete', $event_id) ;

                    $isRepeating = $Proj->isRepeatingForm($event_id, $instrument);
                    $isRepeatingEvent =  $Proj->isRepeatingEvent($event_id);
                    $repeat_instrument = $isRepeating ? $instrument : "";
                    $dq = new \DataQuality();
                    list ($dq_errors, $dq_errors_excluded) = $dq->checkViolationsSingleRecord($record, $event_id, $instrument, array(), $repeat_instance, $repeat_instrument);

                    // normalise data if repeating instrument
                      foreach ($data as $record => $event_data) {
                            foreach ($event_data as $this_event_id => $field_data) {
                                if ($this_event_id == 'repeat_instances') {
                                    $eventNormalized = $event_data['repeat_instances'];
                                } 
                            }
                      }

                    // Check that the submitted form is complete (<instrument_name>_complete == 2) and there are no data quality errors 
                    if((($isRepeating || $isRepeatingEvent) && $eventNormalized[$event_id][$repeat_instrument][$repeat_instance][$instrument . '_complete'] == '2')  
                            || ($data[$record][$event_id][$instrument . '_complete'] == '2'))
                    {
                        if(empty($dq_errors))
                        {

                            $lock_form_events = $this->getProjectSetting('lock-form-event-name', $project_id);
                            $lock_forms = $this->getProjectSetting('lock-form-name', $project_id);
                            $lock_form_type = $this->getProjectSetting('part-config-type', $project_id);

                            // lock all the forms that requested to lock
                            if($lock_form_events != null)
                            {
                                for ($lock = 0; $lock < count($lock_form_events[$i]); $lock++)
                                {
                                    if($lock_form_type[$i][$lock] === "1")
                                    {

                                        for ( $k = 0; $k < count( $lock_forms[$i][$lock] ); $k++ )
                                        {
                                            $this->lockForm($project_id, $record,  $lock_form_events[$i][$lock], $lock_forms[$i][$lock][$k], $repeat_instance, $form_events[$i], $forms[$i]);
                                            if(!$isRepeating && !$isRepeatingEvent)
                                            {
                                                // check if locking form has any children to lock
                                                $lockisRepeating = $Proj->isRepeatingForm($lock_form_events[$i][$lock], $lock_forms[$i][$lock][$k]) ||  $Proj->isRepeatingEvent($lock_form_events[$i][$lock]);
                                                if($lockisRepeating)
                                                {
                                                    //  as repeating lock all instances
                                                    $form_instances = \RepeatInstance::getRepeatFormInstanceList($record, $lock_form_events[$i][$lock], $lock_forms[$i][$lock][$k], $Proj);
                                                    foreach ($form_instances as $instance=>$form_status) 
                                                    {
                                                        if($instance !== $repeat_instance)
                                                        {
                                                              $this->lockForm($project_id, $record,  $lock_form_events[$i][$lock], $lock_forms[$i][$lock][$k], $instance, $form_events[$i], $forms[$i]);

                                                        }
                                                    }
                                                }
                                            }

                                        }
                                    }
                                    else if($lock_form_type[$i][$lock] === "2")
                                    {
                                        $eventForms = $Proj->eventsForms;

                                        foreach ($eventForms as $this_event_id=>$these_forms) {

                                            if($this_event_id == $lock_form_events[$i][$lock])       
                                            {

                                                foreach ($these_forms as $this_key=>$this_form) {

                                                    $bFormFound = false;
                                                    for ( $k = 0; $k < count( $lock_forms[$i][$lock] ); $k++ )
                                                    {
                                                        if($lock_forms[$i][$lock][$k] === $this_form)
                                                        {
                                                            $bFormFound = true;
                                                            continue;
                                                        }
                                                    }
                                                    if(!$bFormFound)
                                                    {  
                                                        $this->lockForm($project_id, $record,  $lock_form_events[$i][$lock], $this_form, $repeat_instance, $form_events[$i], $forms[$i]);
                                                        if(!$isRepeating)
                                                        {
                                                            // check if locking form has any children to lock
                                                            $lockisRepeating = $Proj->isRepeatingForm($lock_form_events[$i][$lock], $this_form) ||  $Proj->isRepeatingEvent($lock_form_events[$i][$lock]);
                                                            if($lockisRepeating)
                                                            {
                                                                //  as repeating lock all instances
                                                                $form_instances = \RepeatInstance::getRepeatFormInstanceList($record, $lock_form_events[$i][$lock], $this_form, $Proj);
                                                                foreach ($form_instances as $instance=>$form_status) 
                                                                {
                                                                    if($instance !== $repeat_instance)
                                                                    {
                                                                          $this->lockForm($project_id, $record,  $lock_form_events[$i][$lock], $this_form, $instance, $form_events[$i], $forms[$i]);

                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                }
                                            }

                                        }
                                    }
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
    }
	
    
    private function isFormLocked($project_id, $record, $event_id, $instrument, $instance, $check_instance = true)
            
    {
        $sql = "select timestamp from redcap_locking_data where project_id = ? and event_id = ?
					and form_name = ? and record = ?";
        
        
        if($check_instance === true)
        {
            $sql .= " and instance = ?";
            $q = $this->query($sql,[$project_id, $event_id, $instrument, $record, $instance]);
        }
        else 
        { 
             $q = $this->query($sql,[$project_id, $event_id, $instrument, $record]); 
        }
           
        if (db_num_rows($q) > 0)
        {
           return  true;  
        }
        return false;
                        
    }
    
    private function lockForm($project_id, $record, $event_id, $instrument, $instance, $save_event_id, $save_instrument)
    {
        if(!$this->isFormLocked($project_id, $record, $event_id, $instrument, $instance))
        {
            $sql = "insert into redcap_locking_data (project_id, record, event_id, form_name, username, timestamp, instance)
                                                    values ($project_id, '" . $record . "', " . $event_id . ",
                                                    '" . $instrument. "', '" . USERID . "', '".NOW."', " . checkNull($instance) . ")";
            $description = "Action: Locking Instrument\nEvent: ".$event_id. "\nInstrument: " .$instrument. "\nInstance: ".checkNull($instance)."\nSaving Event - ".$save_event_id. "\nSaving instrument: " .$save_instrument;


             if (db_query($sql))
            {
                \REDCap::logEvent("Locking Instrument", $description, "LOCK_RECORD", $record, "",  $project_id);
            }
        }

    }
    
    
    public function alert($msg) {
         echo "<script type='text/javascript'>alert('$msg');</script>";
  
    }
    
    private function isHardLocked($event_id, $instrument)
    {
        $hard_lock = $this->getProjectSetting('full-lock', $project_id);
        $override = $this->getProjectSetting('override', $project_id);
        $intermediate_lock = $this->getProjectSetting('intermediate-lock', $project_id);

        $locked = false;

        $override_on = false;
        try
        {
            $override_on = $override && $this->framework->getUser()->isSuperUser();
        }
        catch(\Exception $ex)
        {
            // username not detected, therefore do not set override
        }
      
        if(!$override_on && $hard_lock)
        {
            $locked = true;
        }
        else if (!$override_on && $intermediate_lock) {
            $lock_events = $this->getProjectSetting('inter-lock-event-name', $project_id);
            if($lock_events != null)
            {
                for ( $i = 0; $i < count($lock_events); $i++)
                {

                    if($lock_events[$i] === $event_id)
                    {
                        $lockform = true;
                        $exempt_forms = $this->getProjectSetting('inter-lock-form-name', $project_id);

                        for ($j = 0; $j < count($exempt_forms[$i]); $j++)
                        {
                            if($exempt_forms[$i][$j] === $instrument)
                            {
                                $lockform = false;
                                break;
                            }
                        }

                        if($lockform)
                        {
                            $locked = true;
                        }

                    }

                }
            }
        }
        return $locked; 
        
    }
    
    private function reportLogEvent($event_id, $data, $project_id)
    {
  //       \REDCap::email('nina.clayton@nottingham.ac.uk', 'nina.clayton@nottingham.ac.uk', 'locking', 'reportlogevent');
        $lock_events = $this->getProjectSetting('inter-lock-event-name', $project_id);
        $Proj = new \Project($project_id);

        for ( $i = 0; $i < count($lock_events); $i++)
        {

            if($lock_events[$i] === $event_id)
            {
                $lockform = true;
                $exempt_forms = $this->getProjectSetting('inter-lock-form-name', $project_id);
                
                if($exempt_forms !== null)
                {
                    for ($j = 0; $j < count($exempt_forms[$i]); $j++)
                    {

                          $fields = array_keys($Proj->forms[$exempt_forms[$i][$j]]['fields']);
                        foreach($fields as $this_field)
                        {
                            $fielddatachanges = explode("\n", $data);
                            foreach($fielddatachanges as $fielddata)
                            {

                                if(strpos($fielddata, $this_field.' ') === 0 || strpos($fielddata, $this_field.'(') === 0)
                                {
                                    $lockform = false;
                                    break;
                                }
                            }

                            if(!$lockform)
                            {
                                break;
                            }
                        }

                    }
                }
                break;
            }
            
        }
       
        
        return $lockform;
        
    }
    
    function redcap_module_save_configuration($project_id)
    {
         $full_lock = $this->getProjectSetting('full-lock', $project_id);
        $intermediate_lock = $this->getProjectSetting('intermediate-lock', $project_id);
        
        $config = $this->getProjectSetting( "hard-lock-ts", $project_id);
        if($config == null && ($full_lock || $intermediate_lock))
        {
            $this->setProjectSetting( "hard-lock-ts", date("Y-m-d H:i:s"), $project_id);
        } 
        if($config != null && !$full_lock && !$intermediate_lock)
        {
            $this->removeProjectSetting("hard-lock-ts", $project_id);
        }
         $lasttime = $this->getProjectSetting( "hard-lock-ts", $project_id);
         
    }
    
    private function loadFile($filename) {
        $data = "";
        $file = fopen($this->getModulePath() . $filename, "r");
        if ($file) {
            $data = fread($file, filesize($this->getModulePath() . $filename));
            fclose($file);
        }

        return $data;
    }
    
    // Function called by the CRON to send the scheduled or recurring alerts
    public function violationChecker()
    { 
       foreach($this->framework->getProjectsWithModuleEnabled() as $project_id){

            $lasttime = $this->getProjectSetting( "hard-lock-ts", $project_id);
            if($lasttime !== null)
            {
                $nexttime = date("Y-m-d H:i:s");
                $logEventTable = \REDCap::getLogEventTable($project_id);

                $full_lock = $this->getProjectSetting('full-lock', $project_id);
                $intermediate_lock = $this->getProjectSetting('intermediate-lock', $project_id);


                if($full_lock || $intermediate_lock)
                {
                    $lasttimeint = preg_replace('/[^\d]/', '', $lasttime);
                    $sql = "SELECT DATE_FORMAT(timestamp(log.ts), '%d-%m-%Y %H:%i:%s') as ts, log.ip, log.user, log.event_id, log.pk, log.data_values ".
                             " FROM $logEventTable log ".
                             " WHERE log.project_id = $project_id AND (log.event = 'INSERT' " .
                             " OR log.event = 'UPDATE' OR log.event='DELETE' OR log.event='DOC_DELETE') ".
                             " AND log.object_type = 'redcap_data' AND log.ts >= $lasttimeint" .
                             " ORDER BY log.log_event_id";

                    $params = [$project_id];

                    $query = db_query($sql);
                    
                    $it_inbox = 'ms-nctu-it@exmail.nottingham.ac.uk';
                    global $project_contact_email;
                    if($project_contact_email != "")
                    {
                        $it_inbox = $project_contact_email;
                    }
                    
                    if (db_num_rows($query) > 0)
                    {
                        $Proj = $this->framework->getProject($project_id);
                        $violations = "";

                        $rowcount = 0;
                        while ($row = db_fetch_assoc($query)) {
                            
                            // if intermedicate lock need to check if update is allowed or not
                            if($full_lock || (!$full_lock && $intermediate_lock && $this->reportLogEvent($row['event_id'], $row['data_values'], $project_id)))
                            {
                                $rowcount++;

                                $logtime = $row['ts'];
                                $violations .= "\n\r<br>VIOLATION ".$rowcount.": User=".$row['user']. " Record Id=".$row['pk']." Event Id=".$row['event_id']." Data=".$row['data_values']." at ".$logtime;
                            }
                        }
                        
                        if($rowcount > 0)
                        {
                            \REDCap::email($it_inbox, $it_inbox, 
                                      "HARD LOCK VIOLATION ON ".$Proj->getTitle(), "Data cannot be updated as form has a hard lock - Project=".$Proj->getTitle()."(".$project_id.") ".$violations);
                        }
                    }

                }
                
                $this->setProjectSetting( "hard-lock-ts", $nexttime, $project_id);

             
            }
       }
    }
                    

}
