<?php
class Comm {
	public static $COMM_TYPE_SYSTEM = 0;
	public static $COMM_TYPE_USER = 1;

	private $user_id;
	private $data;

	public function __construct($user_id) {

		$this -> user_id = $user_id;

		$json = F("comm_" . $user_id);
		if (is_null($json)) {//初始化
			$this -> data = $this -> newData();
		}
		else {
			$this -> data = json_decode($json, TRUE);
		}
	}

	public function get($name) {
		return $this -> data[$name];
	}

	public function add($content, $type = 0, $form = null) {
		$count = new SaeCounter();
		$count -> create("comm");
		if (is_null($form)) {
			$form = array("id" => 0);
		}
		$msg = array(
			"id" => $count -> incr("comm"),
			"form" => $form,
			"type" => $type,
			"read" => FALSE,
			"time" => date("Y-m-d H:i", time()),
			"content" => $content
		);
		$this -> data[$type][] = $msg;

		$this -> save();
	}

	public function save() {
		F("comm_" . $this -> user_id, json_encode($this -> data));
	}

	public function clean($name = null) {
		if (is_null($name)) {
			$this -> data = $this -> newData();
		}
		else {
			$this -> data[$name] = null;
		}
		$this -> save();
	}

	private function newData() {
		return array(
			Comm::$COMM_TYPE_SYSTEM => array(),
			Comm::$COMM_TYPE_USER => array()
		);
	}

	public function del($name, $id) {
		$old = $this -> data[$name];
		$size = count($old);
		$new = array();
		foreach ($old as $key => $value) {
			if ($id != $value["id"]) {
				$new[] = $value;
			}
		}
		if (count($new) == $size) {
			return FALSE;
		}
		else {
			$this -> data[$name] = $new;
			$this -> save();
			return TRUE;
		}

	}

	public function read($name, $id) {
		foreach ($this->data[$name] as $key => $value) {
			if ($value["id"] == $id) {
				$this -> data[$name][$key]["read"] = true;
				$this -> save();
				return TRUE;
			}
		}
		return FALSE;
	}

	public function readall() {
		foreach ($this->data as $key1 => $value1) {
			foreach ($this->data[$key1] as $key => $value) {
				$this -> data[$key1][$key]["read"] = true;
			}
		}
		$this -> save();
	}

	public static function log($user_id, $content, $type, $to = null) {
		$log = M("log");
		$log -> add(array(
			"from" => $user_id,
			"to" => $to,
			"type" => $type,
			"create_time" => time(),
			"content" => $content
		));
	}

}
?>