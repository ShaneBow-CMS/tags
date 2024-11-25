<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mtags extends MY_Model {
	protected $table_name = 'tags';

	public function __construct() {
		parent::__construct();
		}

	public function create(&$data) {
		$this->pre_insert($data);
		$success = $this->insert_ignore($this->table_name, $data);
		if (!$success) $this->_throwError();
		$data['id'] = $this->db->insert_id();
		return $data;
		}

	public function find($where) {
		return $this->db
			->get_where($this->table_name, $where)
			->row_array();
		}

	/**
	* Gets the all the tags in the tl_tags table
	*/
	public function all_tags($fields='id,name,brief')
		{
		$this->table_name = 'tags';
		return $this->db
			->select($fields)
			->order_by('name')
			->get($this->table_name)
			->result_array();
		}

	/**
	* return an array of all tag ids for the given object
	*/
	public function get_object_tids($otype,$oid) {
		$tids = [];
		$tags = $this->db->select('tid')
			->get_where('tags2objs', ['otype' => $otype, 'oid' => $oid])
			->result_array();
		foreach($tags as $tag) $tids[] = $tag['tid'];
		return $tids;
		}

	/**
	* remove all records in 'tags2objs' for the given object
	*/
	public function delete_object2tags($otype,$oid) {
		$this->db->where(['otype'=>$otype, 'oid' => $oid])
				->delete('tags2objs');
		}

	/**
	* insert records into 'tags2objs' for the given object:
	* One per tag id in the csv.
	*/
	public function insert_object2tags($otype,$oid,$tid_csv) {
		// insert a record for each tag in 'tags2objs'
		if (strlen($tid_csv)) {
			$tids = explode(',', $tid_csv);
			$values = "";
			foreach ($tids as $tid)
				$values .= ",($otype,$oid,$tid)";
			$values = substr($values,1);

			$sql = "INSERT INTO `tags2objs` (`otype`,`oid`,`tid`) VALUES ".$values.';';
			return $this->db->query($sql);
			}
		else return 0;
		}

	/**
	* deletes then insert records into 'tags2objs' for
	* the given object: One per tag id in the csv.
	*/
	public function update_object_tags($otype,$oid,$tid_csv) {
		$this->delete_object2tags($otype,$oid);
		return $this->insert_object2tags($otype,$oid,$tid_csv);
		}

	public function paginated_pages($base_url, $first, $per_page = 10, $num_links = 5, $where='', $order_by = 'debut DESC') {
		$this->table_name = 'tags2objs t2o';
		// need this join for init pagination
		$this->db->join('pages p', 't2o.oid = p.id AND t2o.otype='.OTYPE_PAGE);
		$paging = $this->init_pagination($base_url, $first, $where, $per_page, $num_links);

		if ($where) $this->db->where($where);
		return $this->db
			->select('p.title,p.lead,p.slug,p.id,p.flags,p.likes,p.yyyymmdd,p.uid,u.nickname'
			.',p.mid,media.type_id as mtype'
			.',cats.title AS cat,cats.slug AS cat_slug'
			.',CONCAT(p.mid,".jpg") AS banner, COUNT(c.oid) as numcomments'
				.",'' as tags" // NOT WORK:	.',GROUP_CONCAT(t2o.tid ORDER BY t2o.tid) AS tags'
				)
		->join('pages p', 't2o.oid = p.id AND t2o.otype='.OTYPE_PAGE) // ,'left outer')
			->join('cats', 'cats.id = p.cid')
			->join('users u', 'u.id = p.uid')
			->join('media', 'p.mid = media.id','left outer')
			->join('comments c', 'c.oid = p.id AND c.otype='.OTYPE_PAGE,'left outer')
			->group_by('t2o.oid')
			->order_by($order_by)
		->get('tags2objs t2o', $paging['per_page'], $first)
			->result_array();
		}

	}
/* _lib/cms/tags/ci/models/Mtags.php */
