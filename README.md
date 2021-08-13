# Locking

This REDCap module provides functionality to lock forms on saving.

## Project-level configuration options

This module provides project-level configuration options, which are only visible to
administrators.

Settings for Locking Instruments when saving a form with completed status:
* Event name of saved instrument
* Saved instrument

	for each saved instrument, define instruments to lock (multiple instruments can be locked on the saved) 
	* Event name
	* Instrument
i.e. if the instrument wants to be locked on save, define instrument in save and lock section
	
Note: 
* If you do not want the form to lock while validation errors exist then use 'Validation Tweaker' external module and check the option, 'Require field validation to pass on form submission'.
* If there are any data quality rule violation, the form will be reset to incomplete to allow these to be fixed or excluded. The form will need to be set to complete again to preform the locking on Save.
* Only the same instance of the saving repeating forms and events can be locked 

