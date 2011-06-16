<?php

class Member_SugarCRM_ethnicity extends Member_SugarCRM_Object {
	
	static $has_many = array(
		'Member'=>'Member'
	);
	
	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(!DataObject::get_one('Member_SugarCRM_ethnicity')) {
			
			foreach (array(
				array("name"=>"White","value"=>"White"),
				array("name"=>"Asian or Asian British","value"=>"Asian or Asian British"),
				array("name"=>"Asian British","value"=>"Asian British (B1)"),
				array("name"=>"Bangladeshi","value"=>"Bangladeshi (B2)"),
				array("name"=>"Indian","value"=>"Indian (B3)"),
				array("name"=>"Pakistani","value"=>"Pakistani (B4)"),
				array("name"=>"Chinese","value"=>"Chinese (B5)"),
				array("name"=>"Other Asian background","value"=>"Other Asian background (B6)"),
				array("name"=>"Black or Black British","value"=>"Black or Black British"),
				array("name"=>"African","value"=>"African (C1)"),
				array("name"=>"Black British","value"=>"Black British (C2)"),
				array("name"=>"Caribbean","value"=>"Caribbean (C3)"),
				array("name"=>"Any other Black background","value"=>"Any other Black background (C4)"),
				array("name"=>"Other Ethnic Group","value"=>"Other Ethnic Group"),
				array("name"=>"White and Asian","value"=>"White and Asian (E1)"),
				array("name"=>"White and Black African","value"=>"White and Black African (E2)"),
				array("name"=>"White and Black Caribbean","value"=>"White and Black Caribbean (E3)"),
				array("name"=>"Any other mixed background","value"=>"Any other mixed background (E4)"),
				array("name"=>"White British","value"=>"White British (F1)"),
				array("name"=>"White Irish","value"=>"White Irish (F2)"),
				array("name"=>"Any other white background","value"=>"Any other white background (F3)")
			) as $ethnic) {
				
				$x = new Member_SugarCRM_ethnicity();
				$x->Name = $ethnic['name'];
				$x->Value = $ethnic['value'];
				$x->write();
			}

			DB::alteration_message("Creating a multi-ethnic society","created");
		}
	}
}