<?php

class Member_SugarCRM extends DataObjectDecorator {
	function extraStatics() {
		return array(
			'db' => array(
				"is_sugar_user"=>"Boolean", //Allows you to turn this shit off
				"sugar_id"=>"varchar",
        		"date_entered"=>"varchar",
		        "date_modified"		=>"varchar",
		        "modified_user_id"	=>"Int",
		        "modified_by_name"=>"varchar",
		        "created_by"=>"varchar",
		        "created_by_name"=>"varchar",
		        "assigned_user_id"	=>"Int",
		        "assigned_user_name"=>"varchar",
		        "salutation"=>"Enum(',Mr.,Ms.,Mrs.,Dr.,Prof.', '')",
		        "email2"=>'Varchar',
		        "benefit_status"=>"Enum(',Attendance Allowance,DLA,ESA,Incapacity,Industrial Injury Benefit,JSA,Other Income Support', '')",
		        "birthdate"=>"Date",
				"last_date_worked_c"=>"Date",
				"special_requirements_c"=>'Text',

			"job_type_sought_other"=>"Text",
			"job_type_sought_sic_other"=>"Text",
		
		
			"cv_profile"	=>"Text",
			"cv_skills"	=>"Text",
			"cv_history"	=>"Text",
			"cv_education"	=>"Text",
			"cv_interests"=>"Text"
        	),
        	'has_one'=>array(
				"ethnicity"=>"Member_SugarCRM_ethnicity",
        		"gender"=>"Member_SugarCRM_gender",
				"job_type_sought_c"=>"Member_SugarCRM_job_type_sought_c",
				"data_protection_c"=>'Member_SugarCRM_data_protection',
        		"UploadedCV"=>'File'
        	),
        	'many_many'=>array(
        		"barriers_to_employment"=>"Member_SugarCRM_barriers_to_employment",
        		"JobsAppliedFor"=>'GEOmodule_Job'
        	),
        	'defaults'=>array(
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
			'phone_home'=>'Phone1',
			'phone_mobile'=>'Phone2',
			'email1'=>'Email',
			'email2'=>'email2',
		
			"primary_address_street"	=> 'Building',
			"primary_address_street_2"	=> 'Street',
			"primary_address_street_3"	=> 'Area',
			"primary_address_city"		=> 'City',
			"primary_address_postalcode"=> 'PCode',
		
			"primary_address_country"	=> 'Country',
//"picture"=>"Avatar"
			"ethnicity"		=>"ethnicity",
			"benefit_status"=>"benefit_status",
        	"gender"		=>"gender",
//MANY TO MANY "barriers_to_employment" => "barriers_to_employment"

			"birthdate"=>"birthdate",
			"last_date_worked_c"=>"last_date_worked_c",
			"job_type_sought_c"=>"job_type_sought_c",
			"job_type_sought_other"=>"job_type_sought_other",
			"job_type_sought_sic_other"=>"job_type_sought_sic_other",
		
		
			"special_requirements_c"=>"special_requirements_c",
			"data_protection_c"=>"data_protection_c",
			"area_c"=>"area_c",

			"cv_profile_c"	=>"cv_profile",
			"cv_skills_c"	=>"cv_skills",
			"cv_history_c"	=>"cv_history",
			"cv_education_c"=>"cv_education",
			"cv_interests_c"=>"cv_interests"
		);
	
	}
	
}

        	
