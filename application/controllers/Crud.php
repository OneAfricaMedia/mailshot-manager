<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud extends CI_Controller {

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

	public function offices()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}

	public function index()
	{
		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	public function offices_management()
	{
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('offices');
			$crud->set_subject('Office');
			$crud->required_fields('city');
			$crud->columns('city','country','phone','addressLine1','postalCode');

			$output = $crud->render();

			$this->_example_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	public function employees_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('employees');
			$crud->set_relation('officeCode','offices','city');
			$crud->display_as('officeCode','Office City');
			$crud->set_subject('Employee');

			$crud->required_fields('lastName');

			$crud->set_field_upload('file_url','assets/uploads/files');

			$output = $crud->render();

			$this->_example_output($output);
	}

	public function sale()
	{
		$crud = new grocery_CRUD();

		$crud->columns('package','silvers','golds','fbl','months','discount','company_name','tigo_pesa_name');
		$crud->fields('package','silvers','golds','fbl','months','discount','company_name','tigo_pesa_name','sales_person','sale_date');

		$crud->set_relation('package','packages','title');

		$crud->where('sales_person',$this->ion_auth->user()->row()->id);
		$crud->where('sale_date',date('Y-m-d'));


		$crud->unset_read_fields('sale_date','sales_person');

		if($this->uri->segment(3)=='success' or  $this->uri->segment(4)=='success' ){
			redirect('crud/sale/read/' . $this->uri->segment(4));
		}

		$crud->field_type('sales_person', 'hidden', $this->ion_auth->user()->row()->id);
		$crud->field_type('sale_date', 'hidden', date('Y-m-d'));

		$crud->callback_after_insert(array($this, 'calculate_total'));
		$crud->callback_after_update(array($this, 'calculate_total'));


		$crud->callback_read_field('total',array($this,'number_format_cb'));
		$crud->callback_read_field('vat',array($this,'number_format_cb'));
		$crud->callback_read_field('total_after_discount',array($this,'number_format_cb'));
		$crud->callback_read_field('discount',array($this,'number_format_cb'));
		$crud->callback_read_field('grand_total',array($this,'grand_total_cb'));

		$output = $crud->render();

		$this->_example_output($output);
	}



	public function report()
	{


		$crud = new grocery_CRUD();

		$crud->set_table('sale');

		$crud->columns('sale_date','sales_person','identification_number','package','silvers','golds','fbl','grand_total','company_name','tigo_pesa_name');


		$crud->set_relation('package','packages','title');
		$crud->set_relation('sales_person','users','first_name');




		$crud->unset_read_fields('sale_date','sales_person');


		$crud->callback_column('grand_total',array($this,'grand_total_cb'));

		$output = $crud->render();

		$this->_example_output($output);
	}

	function number_format_cb($value = '', $primary_key = null)
	{
		return "<span style = 'float:right'> " . number_format($value) . "</span>";
	}

	function grand_total_cb($value = '', $primary_key = null)
	{
		return "<span style = 'float:right;font-weight:bold'> " . number_format($value) . "</span>";
	}

	function calculate_total($post_array,$primary_key)
	{

		$totalPrice = 0;

		$products = array();


		if($this->input->post('silvers'))
			$products[1] = 1;

		if($this->input->post('golds'))
			$products[2] = 2;

		if($this->input->post('fbl'))
			$products[3] = 3;


		if($this->input->post('package'))
		{
			$this->db->where('id',$this->input->post('package'));
			$package  = $this->db->get('packages');

			$totalPrice = $package->row()->price;
		}

		if(count($products)){

			$this->db->where_in('id',$products);
			$products = $this->db->get('products');
			
			foreach($products->result() as $product){

				switch ($this->input->post('package')) {
					case 1:
							$totalPrice = $totalPrice + $product->price_basic;
						break;

					case 2:
							$totalPrice = $totalPrice + $product->price_entry;
							
						break;

					case 3:
							$totalPrice = $totalPrice + $product->price_optimum;
							
						break;
					
					case 4:
							$totalPrice = $totalPrice + $product->price_pro;
							
						break;
				}
			}

		}

		if($this->input->post('months'))
			$totalPrice = $totalPrice * $this->input->post('months'); 




		$priceAfterDiscount =  $totalPrice - $this->input->post('discount');

		$vat = (0.18 * $priceAfterDiscount);

		$grandTotal = $priceAfterDiscount + $vat;


		$this->db->where('id',$primary_key);
		$this->db->update('sale',array('total'=>$totalPrice,'total_after_discount'=>$priceAfterDiscount,'vat'=>$vat,'grand_total'=>$grandTotal,'identification_number'=>date("Ym") . $primary_key));	


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
