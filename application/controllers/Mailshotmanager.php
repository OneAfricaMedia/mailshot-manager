<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailshotmanager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
	}

	public function _example_output($output = null)
	{
		$this->load->view('example.php',(array)$output);
	}


	public function index()
	{
		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}


	public function calendar($year=null,$month=null)
	{

		if(!$this->ion_auth->logged_in())
			redirect("auth/login");

		$prefs = array(
	        'show_next_prev'  => TRUE,
	        'day_type'=>'long',
	        'template'=>'{table_open}<table class="calendar">{/table_open}
    {week_day_cell}<th class="day_header">{week_day}</th>{/week_day_cell}
    {cal_cell_content}<span class="day_listing">{day}</span>{content}&nbsp;{/cal_cell_content}
    {cal_cell_content_today}<div class="today"><span class="day_listing">{day}</span>{content}</div>{/cal_cell_content_today}
    {cal_cell_no_content}<span class="day_listing">{day}</span>&nbsp;{/cal_cell_no_content}
    {cal_cell_no_content_today}<div class="today"><span class="day_listing">{day}</span></div>{/cal_cell_no_content_today}'
		);
		$this->load->library('calendar',$prefs);

		if(!$month){
			$month = date('m');
			$year = date('Y');
		}



		$this->db->select("*,mailshots_schedule.id as schedule_id");
		$this->db->where('MONTH(date)',$month);
		$this->db->where('YEAR(date)',$year);
		$this->db->order_by('schedule_time');
		$this->db->from('mailshots_schedule');
		$this->db->join('company_credits','company_credits.id=mailshots_schedule.company');
		$mailshots = $this->db->get();

		$data = array();

		foreach($mailshots->result() as $mailshot){

			if($mailshot->reviewed == 2 and $mailshot->art == 2){
				$status = 'ready_to_go'; //Ready to go
			}
			else {
				$status = 'not_ready_to_go'; //Not ready to go
			}

			if(isset($data[date('j',strtotime($mailshot->date))]))
				$data[date('j',strtotime($mailshot->date))] .= "<span class = '".$status."'><strong><a href = 'mailshotmanager/mailshots_schedule/edit/" . $mailshot->schedule_id . "'>" . $mailshot->title . " (" . $mailshot->schedule_time . ")</a></strong></span><br><br>";
			else
				$data[date('j',strtotime($mailshot->date))] =  "<span class = '".$status."'><strong><a href = 'mailshotmanager/mailshots_schedule/edit/" . $mailshot->schedule_id . "'>" . $mailshot->title . " (" . $mailshot->schedule_time . ")</a></strong></span><br><br>";
		}



		$number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

		for($i=1;$i<=$number_of_days_in_month;$i++){
			$d = str_pad($i, 2,"0",STR_PAD_LEFT);
			if(isset($data[$i])){
				if (strpos($data[$i], '08:30:00') === false) {
				    $data[$i].="<span class = 'available'><strong><a href = 'mailshotmanager/mailshots_schedule/add?date=". $d ."&month=".$month."&year=".$year."&time=8'>Available (08:30:00)</a></strong></span><br><br>";
				}

				if (strpos($data[$i], '10:00:00') === false) {
				    $data[$i].="<span class = 'available'><strong><a href = 'mailshotmanager/mailshots_schedule/add?date=". $d ."&month=".$month."&year=".$year."&time=8'>Available (10:00:00)</a></strong></span><br><br>";
				}
			}
			else{
				$data[$i]="<span class = 'available'><strong><a href = 'mailshotmanager/mailshots_schedule/add?date=". $d ."&month=".$month."&year=".$year."&time=8'>Available (08:30:00)</a></strong></span><br><br>";
				$data[$i].="<span class = 'available'><strong><a href = 'mailshotmanager/mailshots_schedule/add?date=". $d ."&month=".$month."&year=".$year."&time=10'>Available (10:00:00)</a></strong></span>";
			}
		}



		$dat['calendar'] = $this->calendar->generate($year, $month, $data);

		$this->load->view('calendar',$dat);
	}

	public function customer_calendar($year=null,$month=null)
	{

		$prefs = array(
	        'show_next_prev'  => TRUE,
	        'day_type'=>'long',
	        'template'=>'{table_open}<table class="calendar">{/table_open}
    {week_day_cell}<th class="day_header">{week_day}</th>{/week_day_cell}
    {cal_cell_content}<span class="day_listing">{day}</span>{content}&nbsp;{/cal_cell_content}
    {cal_cell_content_today}<div class="today"><span class="day_listing">{day}</span>{content}</div>{/cal_cell_content_today}
    {cal_cell_no_content}<span class="day_listing">{day}</span>&nbsp;{/cal_cell_no_content}
    {cal_cell_no_content_today}<div class="today"><span class="day_listing">{day}</span></div>{/cal_cell_no_content_today}'
		);
		$this->load->library('calendar',$prefs);

		if(!$month){
			$month = date('m');
			$year = date('Y');
		}

		$this->db->select("*,mailshots_schedule.id as schedule_id");
		$this->db->where('MONTH(date)',$month);
		$this->db->where('YEAR(date)',$year);
		$this->db->order_by('schedule_time');
		$this->db->from('mailshots_schedule');
		$this->db->join('company_credits','company_credits.id=mailshots_schedule.company');
		$mailshots = $this->db->get();

		$data = array();

		foreach($mailshots->result() as $mailshot){


			$status = 'not_ready_to_go'; //Not ready to go
			

			if(isset($data[date('j',strtotime($mailshot->date))]))
				$data[date('j',strtotime($mailshot->date))] .= "<span class = '".$status."'><strong>" . $mailshot->schedule_time . " Not Available</strong></span><br><br>";
			else
				$data[date('j',strtotime($mailshot->date))] =  "<span class = '".$status."'><strong>" . $mailshot->schedule_time . " Not Available</strong></span><br><br>";
		}



		$number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

		for($i=1;$i<=$number_of_days_in_month;$i++){
			if(isset($data[$i])){
				if (strpos($data[$i], '08:30:00') === false) {
				    $data[$i].="<span class = 'available'><strong>08:30:00 Available</strong></span><br><br>";
				}

				if (strpos($data[$i], '10:00:00') === false) {
				    $data[$i].="<span class = 'available'><strong>10:00:00 Available</strong></span><br><br>";
				}
			}
			else{
				$data[$i]="<span class = 'available'><strong>08:30:00 Available</strong></span><br><br>";
				$data[$i].="<span class = 'available'><strong>10:00:00 Available</strong></span>";
			}
		}



		$dat['calendar'] = $this->calendar->generate($year, $month, $data);

		$this->load->view('calendar',$dat);
	}

	public function company_credits()
	{
		if(!$this->ion_auth->logged_in())
			redirect("auth/login");
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->required_fields('contact_person_one','contact_email_one','count','expires');
			$crud->columns('title','contact_person_one','contact_email_one','contact_phone_one','count','expires');

		  	$crud->callback_before_update(array($this, 'capture_before_update'));
		  	$crud->callback_after_insert(array($this, 'capture_before_update'));
		  	$crud->callback_after_update(array($this, 'capture_after_update'));

			$output = $crud->render();


			$this->_example_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}



	public function capture_before_update($post_array,$primary_key)
	{

		switch($this->uri->segment(2)){

			case 'company_credits':
				$this->db->where('id',$primary_key);
				$before = $this->db->get('company_credits')->row();

				$this->db->insert('secrettracker',array('entity_id'=>$this->uri->segment(4), 'before_edit'=>json_encode($before),'UserID'=>$this->ion_auth->user()->row()->id));
			break;
		}
	}

	public function capture_after_update($post_array,$primary_key)
	{


		switch($this->uri->segment(2)){

			case 'company_credits':
				$this->db->where('id',$primary_key);
				$after = $this->db->get('company_credits')->row();

				$this->db->where('entity_id',$primary_key);
				$this->db->where('entity','company_credits');
				$this->db->order_by('SecretTrackerID','desc');
				$this->db->limit(1);
				$last_id = $this->db->get('secrettracker')->row()->SecretTrackerID;

				$this->db->where('SecretTrackerID',$last_id);
				$this->db->update('secrettracker',array('after_edit'=>json_encode($after)));
			break;

			case 'mailshots_schedule':
				$this->db->where('id',$primary_key);
				$after = $this->db->get('mailshots_schedule')->row();

				$this->db->where('entity_id',$primary_key);
				$this->db->where('entity','mailshots_schedule');
				$this->db->order_by('SecretTrackerID','desc');
				$this->db->limit(1);
				$last_id = $this->db->get('secrettracker')->row()->SecretTrackerID;

				$this->db->where('SecretTrackerID',$last_id);
				$this->db->update('secrettracker',array('after_edit'=>json_encode($after)));
			break;
		}
	}



	public function mailshots_schedule()
	{

		if(!$this->ion_auth->logged_in())
			redirect("auth/login");

		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_relation('company','company_credits','title',array('count >' => 0));
		$crud->fields('company','subject','date','schedule_time','reviewed','art','notes');
		$crud->required_fields('company','subject','date','schedule_time','reviewed','art');
		$crud->field_type('schedule_time','dropdown',array('08:30:00'=>'08:30:00','10:00:00'=>'10:00:00'));
		$crud->field_type('art','dropdown',array('1'=>'No','2'=>'Yes'));
		$crud->field_type('reviewed','dropdown',array('1'=>'No','2'=>'Yes'));
		$crud->where('date>=',date('Y-m-d'));

		$crud->callback_after_insert(array($this, 'check_if_approved_insert'));
		$crud->callback_before_update(array($this, 'check_if_approved_update'));
		$crud->callback_after_update(array($this, 'capture_after_update'));

		if( $crud->getState() == 'add' ) { //add these only in edit form
		    $crud->set_css('assets/grocery_crud/css/ui/simple/'.grocery_CRUD::JQUERY_UI_CSS);
		    $crud->set_js_lib('assets/grocery_crud/js/'.grocery_CRUD::JQUERY);
		    $crud->set_js_lib('assets/grocery_crud/js/jquery_plugins/ui/'.grocery_CRUD::JQUERY_UI_JS);
		    $crud->set_js_config('assets/grocery_crud/js/jquery_plugins/config/jquery.datepicker.config.js');
		}

		if($this->input->get('date') ){

			$crud->callback_add_field('date', function () {

				return '<input id="field-date" name="date" type="text" value="'. $this->input->get('date') . '/' . $this->input->get('month') . '/' . $this->input->get('year') . '" maxlength="10" class="datepicker-input form-control hasDatepicker"><a class="datepicker-input-clear ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" tabindex="-1" role="button" aria-disabled="false"><span class="ui-button-text">Clear</span></a>';
			});
		}




		$output = $crud->render();

		$this->_example_output($output);
	}


function check_if_approved_insert($post_array,$primary_key)
{


	if($this->input->post('reviewed')==2 and $this->input->post('art')==2){
		$this->reduce_count($this->input->post('company'));
		$this->send_email($this->input->post('company'),$primary_key);
	}
		 
	return true;
}

function check_if_approved_update($post_array,$primary_key)
{



	$this->db->where('id',$primary_key);
	$schedule = $this->db->get('mailshots_schedule')->row();
	
	$this->db->insert('secrettracker',array('entity_id'=>$primary_key,'entity'=>'mailshots_schedule', 'before_edit'=>json_encode($schedule),'UserID'=>$this->ion_auth->user()->row()->id));

	if($this->input->post('reviewed')==2 and $this->input->post('art')==2){

		if($schedule->reviewed != 2 or $schedule->art !=2){	
			$this->reduce_count($this->input->post('company'));
			$this->send_email($this->input->post('company'),$primary_key);
		}
	}
	return true;
}


function reduce_count($company_id)
{
	$this->db->where('id',$company_id);
	$new_count = $this->db->get('company_credits')->row()->count - 1;
	
	$this->db->where('id',$company_id);
	$this->db->update('company_credits',array('count'=>$new_count));
	
}


function send_email($company_id,$schedule_id)
{
	$this->load->library('email');

    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';

    $this->email->initialize($config);
	

	$this->db->where('id',$company_id);

	$company = $this->db->get('company_credits')->row();


	$this->db->where('id',$schedule_id);
	$schedule=$this->db->get('mailshots_schedule')->row();

	$this->email->from('info@zoomtztools.website', 'Zoom Tanzania Mailshots');
	$this->email->to($company->contact_email_one);

	if($company->contact_email_two)
		$this->email->cc($company->contact_email_one);

	$this->email->bcc('csrteam@zoomtanzania.com');

	$this->email->subject("You have used one Mailshot");


	$data['SubjectLine'] = "You have used one Mailshot";

	
	$message = "Thank you for approving the mailshot with the subject line <strong>" . $schedule->subject . "</strong>.<br><br>The mailshot will be sent on " . date("d-m-Y",strtotime($schedule->date)) . " at " . $schedule->schedule_time;
	$message .= "<br><br>You now have " . $company->count . " mailshots. ";


	if($company->expires){

		if (strpos($company->expires, '1970') === false) 
			$message .= "<br><br>Your mailshots will expire on " . $company->expires;
	}
	
	$message .= "<br><br>If you are running low on mailshots, please contact our sales team";


	$data['MessageBody']=$message;

	$message = $this->load->view('auth/email/mailshots'  , $data, true);
	$messagePlain = $this->load->view('auth/email/mailshots-plain'  , $data, true);

	// echo $message;
	// die();

	$this->email->message($message);
	$this->email->set_alt_message(strip_tags($messagePlain));

	if($this->email->send())
	{

	}
}


	public function orders_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_relation('customerNumber','customers','{contactLastName} {contactFirstName}');
			$crud->display_as('customerNumber','Customer');
			$crud->set_table('orders');
			$crud->set_subject('Order');
			$crud->unset_add();
			$crud->unset_delete();

			$output = $crud->render();

			$this->_example_output($output);
	}

	public function products_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_table('products');
			$crud->set_subject('Product');
			$crud->unset_columns('productDescription');
			$crud->callback_column('buyPrice',array($this,'valueToEuro'));

			$output = $crud->render();

			$this->_example_output($output);
	}

	public function valueToEuro($value, $row)
	{
		return $value.' &euro;';
	}

	public function film_management()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('film');
		$crud->set_relation_n_n('actors', 'film_actor', 'actor', 'film_id', 'actor_id', 'fullname','priority');
		$crud->set_relation_n_n('category', 'film_category', 'category', 'film_id', 'category_id', 'name');
		$crud->unset_columns('special_features','description','actors');

		$crud->fields('title', 'description', 'actors' ,  'category' ,'release_year', 'rental_duration', 'rental_rate', 'length', 'replacement_cost', 'rating', 'special_features');

		$output = $crud->render();

		$this->_example_output($output);
	}

	public function film_management_twitter_bootstrap()
	{
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('twitter-bootstrap');
			$crud->set_table('film');
			$crud->set_relation_n_n('actors', 'film_actor', 'actor', 'film_id', 'actor_id', 'fullname','priority');
			$crud->set_relation_n_n('category', 'film_category', 'category', 'film_id', 'category_id', 'name');
			$crud->unset_columns('special_features','description','actors');

			$crud->fields('title', 'description', 'actors' ,  'category' ,'release_year', 'rental_duration', 'rental_rate', 'length', 'replacement_cost', 'rating', 'special_features');

			$output = $crud->render();
			$this->_example_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	function multigrids()
	{
		$this->config->load('grocery_crud');
		$this->config->set_item('grocery_crud_dialog_forms',true);
		$this->config->set_item('grocery_crud_default_per_page',10);

		$output1 = $this->offices_management2();

		$output2 = $this->employees_management2();

		$output3 = $this->customers_management2();

		$js_files = $output1->js_files + $output2->js_files + $output3->js_files;
		$css_files = $output1->css_files + $output2->css_files + $output3->css_files;
		$output = "<h1>List 1</h1>".$output1->output."<h1>List 2</h1>".$output2->output."<h1>List 3</h1>".$output3->output;

		$this->_example_output((object)array(
				'js_files' => $js_files,
				'css_files' => $css_files,
				'output'	=> $output
		));
	}

	public function offices_management2()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('offices');
		$crud->set_subject('Office');

		$crud->set_crud_url_path(site_url(strtolower(__CLASS__."/".__FUNCTION__)),site_url(strtolower(__CLASS__."/multigrids")));

		$output = $crud->render();

		if($crud->getState() != 'list') {
			$this->_example_output($output);
		} else {
			return $output;
		}
	}

	public function employees_management2()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('employees');
		$crud->set_relation('officeCode','offices','city');
		$crud->display_as('officeCode','Office City');
		$crud->set_subject('Employee');

		$crud->required_fields('lastName');

		$crud->set_field_upload('file_url','assets/uploads/files');

		$crud->set_crud_url_path(site_url(strtolower(__CLASS__."/".__FUNCTION__)),site_url(strtolower(__CLASS__."/multigrids")));

		$output = $crud->render();

		if($crud->getState() != 'list') {
			$this->_example_output($output);
		} else {
			return $output;
		}
	}

	public function customers_management2()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('customers');
		$crud->columns('customerName','contactLastName','phone','city','country','salesRepEmployeeNumber','creditLimit');
		$crud->display_as('salesRepEmployeeNumber','from Employeer')
			 ->display_as('customerName','Name')
			 ->display_as('contactLastName','Last Name');
		$crud->set_subject('Customer');
		$crud->set_relation('salesRepEmployeeNumber','employees','lastName');

		$crud->set_crud_url_path(site_url(strtolower(__CLASS__."/".__FUNCTION__)),site_url(strtolower(__CLASS__."/multigrids")));

		$output = $crud->render();

		if($crud->getState() != 'list') {
			$this->_example_output($output);
		} else {
			return $output;
		}
	}

}
