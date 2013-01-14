<?php

class Member_SugarCRM extends DataObjectDecorator {
	function extraStatics() {
		return array(
			'db' => array(
				"is_sugar_user"=>"Boolean", //Allows you to turn this shit off
				"sugar_id"=>"varchar",
				"date_entered"=>"varchar",
				"date_modified"=>"varchar",
				"salutation"=>"Enum(',Mr.,Ms.,Mrs.,Dr.,Prof.', '')",
				"sugar_note_id"=>"varchar"
			),
			'has_one'=>array(
				"ethnicity"=>"Member_SugarCRM_ethnicity",
				"gender"=>"Member_SugarCRM_gender",
				"UploadedCV"=>'File'
			),
			'many_many'=>array(
			),'defaults'=>array(
				"sugar_id"=>"0",
			)
		);
	}
	
	function onBeforeWrite(){
		$member=$this->owner;
		if($member->is_sugar_user){
			$x=new SugarRestfulService();
			if(!$member->sugar_id){
				if($bean_id=$x->find_member($member->Email)){
					$member->sugar_id=$x->update_member($bean_id,$member,$member->getAllFields());
				}else{
					$member->sugar_id=$x->create_member($member);
				}
			}else{
				$x->update_member($member->sugar_id,$member,$member->getChangedFields());
			}
			if($member->UploadedCV()){
				if(!$member->sugar_note_id){
					$member->sugar_note_id=$x->create_note($member->sugar_id,$member->UploadedCV());
				}else{
					if($raw_file=$x->validate_file_for_upload($member->UploadedCV())){
						$x->update_note($member->sugar_note_id,$member->UploadedCV(),$raw_file);
					}
				}
			}
			
		}
		parent::onBeforeWrite();
	}
	
	function SugarFieldLookup(){
		return array(
			'name'=>'Surname',
			'salutation'=>'salutation',
			'first_name'=>'FirstName',
			'last_name'=>'Surname',
			'email1'=>'Email',
		
			"ethnicity"		=>"ethnicity",
			"gender"		=>"gender"
		);
	}
}