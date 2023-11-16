<?php
class Inventory_item extends CI_Controller {		
    function __construct() {				
        parent::__construct();					
        $this->load->model('staff_model');
		//$this->load->model('consumables/list_departments');	
		if($this->session->userdata('logged_in')){		
            $userdata=$this->session->userdata('logged_in');   
            $user_id=$userdata['user_id'];     
            $this->data['hospitals']=$this->staff_model->user_hospital($user_id);	
            $this->data['functions']=$this->staff_model->user_function($user_id);	
            $this->data['departments']=$this->staff_model->user_department($user_id);	
		}
		$this->data['op_forms']=$this->staff_model->get_forms("OP");		
		$this->data['ip_forms']=$this->staff_model->get_forms("IP");					
	}
    function authorized_party($party_id)
    {

       for($i = 0; $i < count($this->data['parties']); $i++){
            if ($this->data['parties'][$i]->supply_chain_party_id == $party_id)
                return true;
       }
       log_message("error", "Sairam validation failed in authorized_party");
        return false;
    }
    function valid_item($item_id)
    {
        for($i = 0; $i < count($this->data['all_item']); $i++){
            if ($this->data['all_item'][$i]->item_id == $item_id)
                return true;
       }
       log_message("error", "Sairam validation failed in valid_item");
        return false;
    }
    function ne_from($party_id)
    {
        return $this->input->post('from_id') && $party_id != $this->input->post('from_id');
    }

    

    function valid_inventory_quantities()
    {
        $item_ids = $this->input->post('item');
        $quantity_indented = $this->input->post('quantity_indented');
        for($i = 0; $i < count($this->input->post('item')); $i++){
            $item_id = $item_ids[$i];
            $item_quantity = $quantity_indented[$i];
            $sum = 0;
            $inventory_quantities = $this->input->post("quantity_$item_id");
            for($j = 0; $j < count($inventory_quantities); $j++){
                $sum = $sum + (int)$inventory_quantities[$j];
                // log_message("info", "Sairam: $inventory_quantities[$j] $sum");
            }

            if($sum !== (int)$item_quantity){
                log_message("info", "SAIRAM: inventory quantities validation failed $sum quantity_$i ".json_encode($this->input->post("quantity_$item_id"))." ".json_encode($this->input->post(NULL)));
                return false;
            }
            
        }
        return true;
        
    }

    function valid_inventory_dates()
    {
        
        $mfg_date = $this->input->post("mfg_date");
        $exp_date = $this->input->post("expiry_date");
        // log_message("info", "SAIRAM IN VALID INVENTORY DATES ".json_encode($mfg_date));
  

        
        if($mfg_date == null && $exp_date == null)
            return true;
            
        $mfg_timestamp = strtotime($mfg_date);
        $exp_timestamp = strtotime($exp_date.'+23 hour 59 minute 59 second');
        $call_timestamp = strtotime(date("Y-m-d H:i:s"));
        // log_message("info", "SAIRAM from VALID INV DATES $mfg_timestamp $exp_timestamp $call_timestamp");
        if($mfg_timestamp != null && $mfg_timestamp > $call_timestamp){
            // log_message("info", "SAIRAM: inventory dates validation failed");
            return false;
        }

        if($exp_timestamp != null && $exp_timestamp <= $call_timestamp){
            // log_message("info", "SAIRAM: inventory dates validation failed");
            return false;
        }
        if($mfg_timestamp != null && $exp_timestamp != null && $exp_timestamp < $mfg_timestamp){
            // log_message("info", "SAIRAM: inventory dates validation failed");
            return false;
        }
            
            

        
        return true;
    }
    function valid_cost($cost)
    {
        
        if(!$cost || (is_numeric($cost) && $cost >= 0.0)){
            return true;
        }else{
            log_message("Sairam validation failed for cost $cost ". is_numeric($cost));
            return false;
        }
    }
	

    function search_selectize_items()
	{
		if($this->session->userdata('logged_in')){                                                //checking whether user is in logging state or not;session:state of a user.
            $this->data['userdata']=$this->session->userdata('logged_in');                        //taking session data into data array of index:userdata                   
        }	
        else{
            show_404();                                                                          //if user is not logged in then this error will be thrown.
        }
		$this->data['userdata'] = $this->session->userdata('logged_in');
		$user_id = $this->data['userdata']['user_id'];
		$this->load->model('staff_model');
		$this->data['functions'] = $this->staff_model->user_function($user_id);
		$access = -1;
		//var_dump($item_type_id);
		foreach ($this->data['functions'] as $function) {
			if ($function->user_function == "Consumables") {
				$access = 1;
				break;
			}
		}
		if ($access != 1) {
			show_404();
		}

		$this->load->model('consumables/indent_report_model');
		$items = $this->indent_report_model->search_items_selectize();
		$res = array('items' => $items);
		echo json_encode($res);
	}

    function edit_inventory_item_list($item_id, $scp_id)
	{
		if($this->session->userdata('logged_in')){                                                //checking whether user is in logging state or not;session:state of a user.
            $this->data['userdata']=$this->session->userdata('logged_in');                        //taking session data into data array of index:userdata                   
        }	
        else{
            show_404();                                                                          //if user is not logged in then this error will be thrown.
        }
		$this->data['userdata'] = $this->session->userdata('logged_in');
		$user_id = $this->data['userdata']['user_id'];
		$this->load->model('staff_model');
		$this->data['functions'] = $this->staff_model->user_function($user_id);
		$access = -1;
		//var_dump($item_type_id);
		foreach ($this->data['functions'] as $function) {
			if ($function->user_function == "Consumables") {
				$access = 1;
				break;
			}
		}
		if ($access != 1) {
			show_404();
		}

		$this->data['userdata'] = $this->session->userdata('indent');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('item', "Item id required", 'required');
        $this->form_validation->set_rules('scp_id', "Supply Chain Party Id required", 'required');
		$user = $this->session->userdata('logged_in');
		$this->data['title'] = "Inventory Item List";
		$this->load->view('templates/header', $this->data);
		$this->load->view('templates/leftnav', $this->data);
		$this->load->model('consumables/indent_report_model');
		$this->data['all_item_type'] = $this->indent_report_model->get_data("item_type");
		$this->data['all_item'] = $this->indent_report_model->get_data("item");
		$this->data['parties'] = $this->indent_report_model->get_data("party");
		log_message('info', "SAIRAM FROM GET ITEM $scp_id $item_id");
		$validations = array(
			array(
				'field' => 'item',
				'label' => 'Item',
				'rules' => 'trim|xss_clean'
			), 
			
			array(
				'field' => 'item_type', 
				'rules' => 'trim|xss_clean', 
			), 
			// more validations
			

		);
		$this->form_validation->set_rules($validations);
		// $this->form_validation->set_message('message', 'Please input missing details.');
		if ($this->form_validation->run() === FALSE) {
			if(isset($scp_id) && isset($item_id)){
				$this->data['mode'] = 'search';
				$this->data['search_inventory_summary'] = $this->indent_report_model->get_item_summary_edit($item_id, $scp_id);
				log_message("info", "SAIRAM from URL ".json_encode($this->data['search_inventory_summary']));
				$this->load->view('pages/consumables/inventory_item_list_view', $this->data);
			}else{
				$this->load->view('pages/consumables/inventory_item_list_view', $this->data);
			}
		} else if ($this->input->post('search')) {
			$this->data['mode'] = "search";
			$item_id = $this->input->post('item');
			$scp_id = $this->input->post('scp_id');
			$this->data['search_inventory_summary'] = $this->indent_report_model->get_item_summary_edit($item_id, $scp_id);
            log_message("info", "SAIRAM from URL ".json_encode($this->data['search_inventory_summary']));
			log_message("info", "SAIRAM ".json_encode($this->data['search_inventory_summary']));
			$this->load->view('pages/consumables/inventory_item_list_view', $this->data);
		} else {
			show_404();
		}
		$this->load->view('templates/footer');
	} //ending of get indent summary method.

    function edit($inventory_id)
    {
        if($this->session->userdata('logged_in')){                                                //checking whether user is in logging state or not;session:state of a user.
            $this->data['userdata']=$this->session->userdata('logged_in');                        //taking session data into data array of index:userdata                   
        }	
        else{
            show_404();                                                                          //if user is not logged in then this error will be thrown.
        }
		$this->data['userdata'] = $this->session->userdata('logged_in');
		$user_id = $this->data['userdata']['user_id'];
		$this->load->model('staff_model');
		$this->data['functions'] = $this->staff_model->user_function($user_id);
		$access = -1;
		//var_dump($item_type_id);
		foreach ($this->data['functions'] as $function) {
			if ($function->user_function == "Consumables") {
				$access = 1;
				break;
			}
		}
		if ($access != 1) {
			show_404();
		}

		$this->load->model('consumables/indent_report_model');
        $this->load->helper('form');
		$this->load->library('form_validation');
        $this->form_validation->set_rules('inventory_id', 'Inventory ID required', 'required');
        $this->form_validation->set_rules('cost', 'Cost value provided is not valid', 'callback_valid_cost');
        // $this->form_validation->set_rules('batch', '');
        $this->data['title'] = "Edit Inventory Item";
		$this->load->view('templates/header', $this->data);
		$this->load->view('templates/leftnav', $this->data);

        $item = $this->indent_report_model->get_inventory_item($inventory_id)[0];
        $this->data['item'] = $item;
        if(!$this->valid_inventory_dates() || $this->form_validation->run() === FALSE){
            $this->load->view('pages/consumables/edit_inventory_item_view', $this->data);
        }else{
            
            // log_message('info', "SAIRAM: new values are ".json_encode($this->input->post(NULL, TRUE)));
            $this->indent_report_model->edit_inventory_item($item->indent_id, $item->item_id, $item->itemwise_sr_no, $item);
            $this->data['all_item_type'] = $this->indent_report_model->get_data("item_type");
		    $this->data['all_item'] = $this->indent_report_model->get_data("item");
		    $this->data['parties'] = $this->indent_report_model->get_data("party");
            $this->data['search_inventory_summary'] = $this->indent_report_model->get_item_summary_edit($item->item_id, $item->from_party);           
            $this->load->view('pages/consumables/inventory_item_list_view', $this->data);
        }
    }

}
