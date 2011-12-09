<?php 

class SugarRestfulService extends RestfulService {
	
	protected $baseURL;
	protected $authUsername=false;
	
	protected static $defaultURL,$jsonUsername, $jsonPassword;
	
	protected static $response;
	protected static $session_id;
	
	
	function __construct($base=null,$expiry=3600){
		parent::__construct(self::$defaultURL,$expiry);
		$this->request();
	}
	
	function set_url($url){
		self::$defaultURL=$url;
	}
	
	function set_username_password($user,$password){
		self::$jsonUsername=$user;
        self::$jsonPassword=md5($password);
	}
	
	public function request($subURL = '', $method = "POST", $data = null, $headers = null, $curlOptions = array()) {
		
		$data = array(
		    'user_auth' => array(
		        'user_name' => self::$jsonUsername,
		        'password' => self::$jsonPassword,
		        ),
		    );
		if($result=$this->run('login',$data)){
		
			if(isset($result->id)){
			
				self::$session_id=$result->id;
				return true;
			}
		}
	}
	
	public function sessionId(){
		return self::$session_id;
	}
	
	public function run($method,$data,$input_type='JSON'){

		$rest_data=($input_type=='JSON')?json_encode($data):serialize($data);

		$this->setQueryString($data);
		if($x=parent::request(null, "POST", array(
			'method' => $method,
			'input_type' => $input_type,
			'response_type' => 'JSON',
			'rest_data' => $rest_data,
		), null,array())){
			return json_decode($x->getBody());
		}
	}

	public function find_member($email){
	
		$data = array(
			'session' => $this->sessionId(),
			'module' => 'sh_Clients',
			'query' => "(sh_clients.id in (select eabr.bean_id from email_addr_bean_rel eabr join email_addresses ea on eabr.email_address_id = ea.id where eabr.bean_module=\"sh_clients\" and ea.email_address LIKE \"$email\" and ea.deleted=\"0\" and eabr.deleted=\"0\"))",
			'order_by' => 'sh_clients.id',
			'offset'	=> 0,
			'select_fields'=>'id',
			'link_name_to_fields_array'=>null,
			'max_results'=>'1',
			'deleted'=>false
		);

		if($result=$this->run('get_entry_list',$data)){
			if($result->result_count>0){
				foreach ($result->entry_list as $entry){
					return $entry->id;
				}
			}
		}
		return false;
	}
	
	
	public function create_member($member){

		$data = array(
		    'session' => $this->sessionId(),
		    'module' => 'sh_Clients',
		    'name_value_list' => $this->set_field_map($member, $member->getAllFields(),array(
				"source"=>"Web Site"
			))
		);
		if($result=$this->run('set_entry',$data)){	
			return $result->id;
		}else{
			Log::log("Could not create Member:{$member->ID} on sugar", "1");
		}
	}

	public function update_member($bean_id,$member,$fields){

		$data = array(
		    'session' => $this->sessionId(),
		    'module' => 'sh_Clients',
		    'name_value_list' => $this->set_field_map($member, $fields,array(
					"source"=>"Web Site",
					"id"=>$bean_id
				)
			)
		);
		if($result=$this->run('set_entry',$data)){
			return $result->id;
		}else{
			Log::log("Could not create Member:{$member->ID} on sugar", "1");
		}
	}

	public function validate_file_for_upload($file){
		
		$target_path=$file->getFullPath();
		
		if(@file_exists($target_path)){
			$fp = fopen ($target_path, "r");
			$raw_file = base64_encode(fread($fp, filesize($target_path)));
			fclose($fp);
			return $raw_file;
		}else{
			return false;
		}
	}
	
	public function create_note($bean_id,$file){

		if($raw_file = validate_file_for_upload($file)){
			
			$data = array(
				'session' => $this->sessionId(),
				'module' => 'Notes',
				'name_value_list' => array(
					"name"=>$file->Title,
					"parent_type"=>'sh_Clients',
					"parent_id"=>$bean_id
				)
			);

			if($result=$this->run('set_entry',$data)){
				return update_note($result->id,$file,$raw_file);
			}
		}
	}

	public function update_note($noteId,$file,$raw_file){
	
		$this->setQueryString(array(
			'method' => 'set_note_attachment',
			'input_type' => 'JSON',
			'response_type' => 'JSON',
		));
		
		if($x=parent::request(null, "POST", array(
			'rest_data'=>json_encode(array(
			'session' => $this->sessionId(),
			'attachment'=>array(
				"id" => $noteId,
				"filename" => "{$file->Title}.{$file->getExtension()}",
				"file" => $raw_file,
			)
		))))){
			$result=json_decode($x->getBody());
			return $result->id;
		}else{
			Debug::show($result);
			Debug::show('set_note_attachment failed');
		}

	}

	public function create_job($job){

		$data = array(
			'session' => $this->sessionId(),
			'module' => 'geo_GEO',
			'name_value_list' => $this->set_field_map($job, $job->SugarFieldLookup())
		);
		
		if($result=$this->run('set_entry',$data)){	
			return $result->id;
		}else{
			Log::log("Could not create Job:{$job->ID} on sugar", "1");
		}
	}
	
	public function update_job($bean_id,$member,$fields){

		$data = array(
		    'session' => $this->sessionId(),
		    'module' => 'geo_GEO',
		    'name_value_list' => $this->set_field_map($member, $member->SugarFieldLookup(),array(
					"id"=>$bean_id
				)
			)
		);
		
		if($result=$this->run('set_entry',$data)){
			return $result->id;
		}else{
			Log::log("Could not create Member:{$member->ID} on sugar", "1");
		}
	}
	
	
	public function apply_for_job($member_sugar,$job_sugar){
	
		$data = array(
		    'session' => $this->sessionId(),
			'module_name'=>'sh_Clients',
			'module_id'=>$member_sugar,
			'link_field_name'=>'sh_clients_geo_geo',
			'related_ids'=>array($job_sugar)
		);
	
		if($result=$this->run('set_relationship',$data)){
			Debug::show($data);
			Debug::show($result);
			return $result->created;
		}else{
			Debug::show("Member:{$member} could not apply for {$job} on sugar", "1");
		}
	}
	
	
	public function set_field_map($member,$filter=array(),$details=array("source"=>"Web Site")){
	
	
		$has_ones=$member->has_one();
		foreach($member->SugarFieldLookup() as $fieldName => $fieldType) {

			if (in_array($fieldType,$filter)){
			
				if($member->$fieldType){
					
					if(in_array($fieldType,$has_ones)){
						
						if(isset($member->$fieldType()->Value)){
							$details[]=array('name'=>$fieldName,'value'=>$member->$member->$fieldType()->Value);
						}	
					
					}else{
						
						if ($member->obj($fieldType) instanceof HTMLText) {
							$value=Convert::html2raw($member->$fieldType);
						}else{
							$value=$member->$fieldType;
						}
						$details[]=array('name'=>$fieldName,'value'=>$value);
					}
				}
			}
		}
		
		return $details;
	}
	
	
}