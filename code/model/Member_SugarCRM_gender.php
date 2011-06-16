<?php

class Member_SugarCRM_gender extends Member_SugarCRM_Object {
	
	static $has_many = array(
		'Member'=>'Member'
	);

	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		$models = DataObject::get_one('Member_SugarCRM_gender');
		if(!$models) {
			$x = new Member_SugarCRM_gender();
			$x->Name = "male";
			$x->Value = "Male (A1)";
			$x->write();
			
			$x = new Member_SugarCRM_gender();
			$x->Name = "female";
			$x->Value = "Female (A2)";
			$x->write();
			DB::alteration_message("Starting battle of the sexes","created");
		}
	}
}
