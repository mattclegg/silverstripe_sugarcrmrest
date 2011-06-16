<?php

class SugarAdmin extends ModelAdmin {

	public static $managed_models = array(
		'Member_SugarCRM_job_type_sought_c',
		'Member_SugarCRM_ethnicity'
	);
	
	static $url_segment = 'sugar-settings';
	static $menu_title = 'sugarCRM';

}
