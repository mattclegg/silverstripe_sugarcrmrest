<?php

class Member_SugarCRM_barriers_to_employment extends Member_SugarCRM_Object {
	
	static $belongs_many_many = array(
		'Member'=>'Member'
	);
	
	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(!DataObject::get_one('Member_SugarCRM_barriers_to_employment')) {
			
			foreach (array(
				array("name"=>"None","value"=>"None"),
				array("name"=>"Core Skills","value"=>"Core Skills"),
				array("name"=>"Ex Offender","value"=>"Ex Offender"),
				array("name"=>"Financial and Benefit","value"=>"Financial and Benefit"),
				array("name"=>"Health problems","value"=>"Health problems"),
				array("name"=>"Limited Work Experience","value"=>"Limited Work Experience"),
				array("name"=>"Lone Parent","value"=>"Lone Parent"),
				array("name"=>"Misuse of Drugs or Alcohol","value"=>"Misuse of Drugs or Alcohol"),
				array("name"=>"Other Caring Responsibility","value"=>"Other Caring Responsibility"),
				array("name"=>"Transport","value"=>"Transport"),
				array("name"=>"Weak Literacy Numeracy","value"=>"Weak Literacy\/Numeracy")
			) as $ethnic) {
				
				$x = new Member_SugarCRM_barriers_to_employment();
				$x->Name = $ethnic['name'];
				$x->Value = $ethnic['value'];
				$x->write();
			}

			DB::alteration_message("Creating barriers to employment","created");
		}
	}
	
}