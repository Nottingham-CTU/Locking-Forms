# Locking

This REDCap module provides functionality to lock forms on saving.

## Project-level configuration options

This module provides project-level configuration options, which are only visible to users with designer privileges.

###Settings for Hard locking
* Full Hard Lock (only available for administrators(super users) to remove unlock and any save options for a form and options to add a new record.
* Intermediate Hard Lock (only available for administrators(super users) to remove unlock and any save options for a form and options to add a new record.
define all events and instrument that is EXEMPT in the event that should no longer editable
	* Event name
	* Instrument EXEMPT in event
* Override Hard Lock	 (only available for administrators(super users) to override the removal of unlock button for administrators(super users) ONLY)

Other modules can find out if forms are hard locked by calling Locking Forms module, to stop any module updates to the database.
<br>The follow details are required:
* **event_id** int, The event ID number of the current data entry form, in which the event_id corresponds to a defined event in a longitudinal project. For classic projects, there will only ever be one event_id for the project.
* **instrument** string, The name of the current data collection instrument (i.e., the unique name, not the instrument label). 

#### Example usage

```php
if ( $this->isModuleEnabled('locking_forms') )
{

    $Locking = \ExternalModules\ExternalModules::getModuleInstance('locking_forms');
    if($Locking->isHardLocked($event_id, $instrument))
    {

        // add custom code related to modules to disable/hide links and fields if the database is hard locked
    }
}
```

### Settings for Locking Instruments when saving a form with completed status:
* Event name of saved instrument
* Saved instrument
* Do not allow new instances (only available for administrators(super users) to remove save options so new instance of repeating form or event cannot be added. The saved instrument must be locked).
	for each saved instrument, define instruments to lock (multiple instruments can be locked on the saved) 
	* Event name
	* Instrument/ Instrument EXEMPT in event
i.e. if the instrument wants to be locked on save, define instrument in save and lock section
	
Note: 
* IT will receive an email if there are any data violations (data saved once a full or intermediate lock (hard lock) has been set regardless if override is checked or not.
* If you do not want the form to lock while validation errors exist then use 'Validation Tweaker' external module and check the option, 'Require field validation to pass on form submission'.
* If there are any data quality rule violation, the form will be reset to incomplete to allow these to be fixed or excluded. The form will need to be set to complete again to preform the locking on Save.
* Only the same instance of the saving of repeating form and repeating events can be locked. 
  If the saving form is NOT a repeating form but a configured locking form is a repeating form, all instances of the repeatable form will be locked.

