<?php

class Member_SugarCRM_data_protection extends Member_SugarCRM_Object {
	
	static $has_many = array(
		'Member'=>'Member'
	);

	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(!DataObject::get_one($this->ClassName)) {
			$x = new $this->ClassName();
			$x->Name = "Confirmed";
			$x->Value = "S2O participant has confirmed willingness to be contacted in relation to evaluation research";
			$x->write();
			
			$x = new $this->ClassName();
			$x->Name = "Declined";
			$x->Value = "S2O participant has declined to be contacted in relation to evaluation research";
			$x->write();
			DB::alteration_message("Providing Data Protection","created");
		}
	}
}
