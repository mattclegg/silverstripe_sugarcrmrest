<?php
class Member_SugarCRM_job_type_sought_c extends Member_SugarCRM_Object {
	
	static $has_many = array(
		'Member'=>'Member'
	);
	
	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(!DataObject::get_one('Member_SugarCRM_job_type_sought_c')) {
			
			foreach (array(
				array("name"=>"Administrative and Secretarial","value"=>"Administrative and Secretarial Occupations"),
				array("name"=>"Ass. Prof. and Tech. Occupations","value"=>"Ass. Prof. and Tech. Occupations"),
				array("name"=>"Elementary Occupations","value"=>"Elementary Occupations"),
				array("name"=>"Manager and Senior Officials","value"=>"Managers and Senior Officials"),
				array("name"=>"Personal Trades Occupations","value"=>"Personal Trades Occupations"),
				array("name"=>"Process Plant and Machine Operators","value"=>"Process Plant and Machine Operators"),
				array("name"=>"Professional Occupations","value"=>"Professional Occupations"),
				array("name"=>"Sales and Customer Service","value"=>"Sales and Customer Service Occupations"),
				array("name"=>"Skilled Trades Occupations","value"=>"Skilled Trades Occupations")
			) as $ethnic) {
				
				$x = new Member_SugarCRM_job_type_sought_c();
				$x->Name = $ethnic['name'];
				$x->Value = $ethnic['value'];
				$x->write();
			}

			DB::alteration_message("Creating a jobs","created");
		}
	}
}