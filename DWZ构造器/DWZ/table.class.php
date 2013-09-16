<?php
class DWZ_table {
	private $_data = null;
	private $_count = null;

	private $_table = "";
	private $_where = array();
	private $_order = "";
	private $_numPerPage;
	private $_pageNum;
	private $_join = null;

	public $keyList = null;
	public $thead = array();
	public $key = array();
	public $id = "id";

	public function __construct($table) {
		$this -> _table = $table;
		$this -> _numPerPage = C("NumPerPage");

		$this -> _pageNum = isset($_REQUEST["pageNum"]) ? $_REQUEST["pageNum"] - 1 : 0;

		if (isset($_REQUEST["DWZ_key"]) && isset($_REQUEST["DWZ_value"])) {
			$this -> _where = array($_REQUEST["DWZ_key"] => $_REQUEST["DWZ_value"]);
		}
	}

	public function setWhere($where) {
		$this -> _where = array_merge($where, $this -> _where);
	}

	public function setOrder($order) {
		$this -> _order = $order;
	}

	public function setJoin($join) {
		$this -> _join = $join;
	}

	public function getData() {
		if (is_null($this -> _data)) {
			$model = M($this -> _table);
			$this -> _data = $model -> where($this -> _where) -> join($this -> _join) -> page($this -> _pageNum + 1, $this -> _numPerPage) -> order($this -> _order) -> select();
		}
		return $this -> _data;
	}

	public function setData($data) {
		$this -> _data = $data;
	}

	public function getCount() {
		if (is_null($this -> _count)) {
			$this -> _count = M($this -> _table) -> where($this -> _where) -> count();
		}
		return $this -> _count;
	}

	public function getNumPerPage() {
		return $this -> _numPerPage;
	}

	public function getPageNum() {
		return $this -> _pageNum;
	}

	public function getKeyList() {
		if (is_null($this -> keyList)) {
			$tmp = array();
			foreach ($this->key as $key => $value) {
				$tmp[$value] = $this -> thead[$key];
			}
			return $tmp;
		}
		return $this -> keyList;
	}

	public function display() {
		return array(
			"id" => $this -> id,
			"data" => $this -> getData(),
			"key" => $this -> key,
			"thead" => $this -> thead,
			"keylist" => $this -> getKeyList(),
			"numPerPage" => $this -> _numPerPage,
			"pagenum" => $this -> _pageNum,
			"count" => $this -> getCount()
		);
	}

}
?>