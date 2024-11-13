<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tag extends MY_Controller {

	function __construct() {
		parent::__construct();
		}

	private function _write_tagcloud() {
		$tags = $this->mtags->all_tags();
		$fname = realpath(APPPATH.'../public_html/assets/files/').'/tagcloud.div';
		$file = fopen($fname, 'w');
		foreach ($tags as $tag)
			fwrite($file,"<i tag=\"{$tag['id']}\" tip=\"{$tag['brief']}\">{$tag['name']}</i>".PHP_EOL);
		fclose($file);
		}

	/**
	* manage - admin page for managing tags
	*/
	public function manage() {
		$this->require_min_level(MIN_ADMIN_LEVEL);
		$this->load->model('mtags');
		$data['tags'] = $this->mtags->all_tags();
		$this->load->view('admin/cms-tags-manager', $data);
		}

	/**
	* create - processes AJAX request to add a new tag
	*/
	public function create() {
		return $this->post();
		}

	/**
	* post - processes AJAX request to
	* add a new tag or update existing tag
	*/
	public function post() {
		$this->load->helper('ajax');
		$usr = $this->session->userdata('usr');
		if (!$this->_usr)
			respond(EXIT_ERR_LOGIN, 'login', "Log in required");
		$this->load->library('form_validation');
		if ($this->form_validation->run('tag-create') == FALSE)
			dieInvalidFields();

		$this->load->model('mtags');

		$id = isset($_POST['id'])? intval($_POST['id']) : FALSE;
		$tag['name'] = $_POST['name'];
		$tag['brief'] = $_POST['brief'];

		try {
			if ($id)
				$data = $this->mtags->update($id, $tag);
			else
				$data = $this->mtags->create($tag);
			$this->_write_tagcloud();
			}
		catch (Exception $e) {db_error($e->getMessage());}
		respond(0, "SUCCESS", $data);
		}

	public function seed() {
		$this->require_role(ROLE_ADMIN);
		$this->load->model('mseeder');
		$this->mseeder->reseed_tables('Create Tags Tables', [
			'tags','tags2objs'
			]);
		}
	}
