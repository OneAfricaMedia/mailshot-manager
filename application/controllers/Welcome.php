<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{

		redirect('mailshotmanager/calendar');
		// if($this->ion_auth->logged_in()){
		// 	$this->db->select('*,users.id as user_id');
		// 	$this->db->order_by('first_name');
		// 	$this->db->from('users');
		// 	$this->db->join('teams', 'users.team=teams.id');
		// 	$data['users'] = $this->db->get();

		// 	$user_id = $this->ion_auth->user()->row()->id;
		// 	$this->db->where('voter_id',$user_id);
		// 	$this->db->where('Month(vote_date) ', date("m"),TRUE);
		// 	$this->db->where('Year(vote_date) ', date("Y"),TRUE);
		// 	$hasVoted = $this->db->get('voted');

		// 	if($hasVoted->num_rows()){
		// 		// $this->session->set_flashdata(array("AlreadyVoted"=>1));
		// 		$data['hasVoted']=1;
		// 	}


		// 	$this->load->view('vote',$data);
		// }
		// else{
		// 	redirect('auth/login');
		// }
	}


	function submitvote()
	{
		if($this->ion_auth->logged_in()){

			$user_id = $this->ion_auth->user()->row()->id;
			$this->db->where('voter_id',$user_id);
			$this->db->where('Month(vote_date) ', date("m"),TRUE);
			$this->db->where('Year(vote_date) ', date("Y"),TRUE);
			$hasVoted = $this->db->get('voted');


			$reasons = "1. " . $this->input->post("reason1");
			$reasons .= "<br>2. " . $this->input->post("reason2");

			if($this->input->post('reason3'))
				$reasons .= "<br>3. " . $this->input->post("reason3");


			if($hasVoted->num_rows()){
				$this->session->set_flashdata(array("AlreadyVoted"=>1));
			}
			else
			{
				$vote = array(
					"vote_date"=>date("Y-m-d"),
					// "voter_id"=>$user_id,
					"user_id"=>$this->input->post("candidate_id"),
					"reasons"=> $reasons
				);
				$this->db->insert("votes",$vote);
				$this->db->insert('voted',array('vote_date'=>date("Y-m-d"),'voter_id'=>$user_id));

				$this->session->set_flashdata(array("Voted"=>1));
			}

			redirect(base_url());

		}

		else{
			redirect("auth");
		}
	}


	public function results()
	{
		$this->db->from('winners');
		$this->db->join('users','users.id=winners.user_id');
		$this->db->join('teams','users.team=teams.id');
		$this->db->order_by('win_date','desc');
		$data['winners'] = $this->db->get();
		$this->load->view('winners',$data);
	}

	public function admin_results()
	{

		$this->db->select("reasons,user_id");
		$this->db->where('Month(vote_date) ', date("m"),TRUE);
		$this->db->where('Year(vote_date) ', date("Y"),TRUE);
		$reasons = $this->db->get("votes");

		$this->db->where("id <>", 1);
		$data['users'] = $this->db->get('users');


		$data['users'] = $this->db->query( " SELECT *, COUNT(user_id) as votes  FROM votes INNER JOIN users ON users.id = votes.user_id WHERE MONTH(vote_date) = " . date("m") . " AND YEAR(vote_date) = " . date("Y") . " GROUP BY user_id ORDER BY votes desc");


		$votes_reason = array();

		foreach($reasons->result() as $reason)
		{

			// echo "1";
			if(isset($votes_reason[$reason->user_id])){

				$votes_reason[$reason->user_id] .=  "<br><br>" . $reason->reasons;
			}
			else{

				$votes_reason[$reason->user_id] = $reason->reasons;
			}
		}

		$data['votes_reason'] = $votes_reason;

		$this->load->view('approve_votes',$data);
	}



}
