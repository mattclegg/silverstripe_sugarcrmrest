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
			self::$session_id=$result->id;
			return true;
		}
	}
	
	public function sessionId(){
		return self::$session_id;
	}
	
	/**
	
	public function get_entry(){	
	
	
	$parameters = array(
	    'session' => $this->sessionId,
	    'module' => 'sh_Clients',
	    'name_value_list' => array(
	        array('name' => 'name', 'value' => 'New Account'),
	        array('name' => 'description', 'value' => 'This is an account created from a REST web services call'),
        ),
    );
	
	$json = json_encode($parameters);
	$postArgs = array(
	    'method' => 'get_entry',
	    'input_type' => 'JSON',
	    'response_type' => 'JSON',
	    'rest_data' => $json,
    );
	get_entry
	
	
	}
	**/
	
	public function run($method,$data){
	
		$this->setQueryString($data);
	
		if($x=parent::request(null, "POST", array(
		    'method' => $method,
		    'input_type' => 'JSON',
		    'response_type' => 'JSON',
		    'rest_data' => json_encode($data),
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
			'max_results'=>'100',
			'deleted'=>false
		);

		if($result=$this->run('get_entry_list',$data)){
			if($result->total_count>0){
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
		    'name_value_list' => $this->set_field_map($member, null,array(
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
			Debug::show($result);
		
			return $result->id;
		}else{
			Log::log("Could not create Member:{$member->ID} on sugar", "1");
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
						$details[]=array('name'=>$fieldName,'value'=>$member->$fieldType);
					}
				}
			}
		}
		return $details;
	}
	
	
}