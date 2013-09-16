<?php
class DWZ_form {
	const CNAME = "cname";
	const TYPE = "type";
	const TYPEDATA = "typedata";

	private $validate;
	private $form = null;
	public $check = null;
	public $model = null;
	public $data = array();

	public function __construct($name) {
		import("@.FormCheck." . $name);
		$classname = "FormCheck_" . $name;
		$this -> check = new $classname();
		$this -> validate = $this -> check -> getValidate();
	}

	public function display() {
		if (!is_null($this -> model) && isset($_REQUEST["id"])) {
			$this -> data = $this -> model -> find($_REQUEST["id"]);
		}

		if (is_null($this -> form)) {
			$this -> form = array();
			foreach ($this->validate as $name => $arr) {
				foreach ($arr as $key => $value) {
					switch ($key) {
						case FormCheck::REQ :
							$this -> attr($name, "class", "required");
							break;
						case DWZ_form::CNAME :
							$this -> form[$name]["cname"] = $value;
							break;
						case DWZ_form::TYPE :
							$this -> form[$name]["type"] = $value;
							$this -> form[$name][$value] = $arr[DWZ_form::TYPEDATA];
							break;
						case FormCheck::IN :
							$this -> form[$name]["type"] = "radio";
							$this -> form[$name]["radio"] = $value;
							break;

						default :
							break;
					}
				}
				if (isset($this -> data[$name])) {
					$this -> attr($name, "value", $this -> data[$name]);
				}
			}
			$this -> attrHandle();
		}

		return $this -> form;
	}

	private function attr($name, $key, $value) {
		if (is_null($this -> form[$name][$key])) {
			$this -> form[$name]['_attr'][$key] = $value;
		}
		else {
			$this -> form[$name]['_attr'][$key][$value] .= " " . $value;
		}
	}

	private function attrHandle() {
		foreach ($this->form as $name => $arr) {
			$attr = "";
			foreach ($arr['_attr'] as $key => $value) {
				$attr .= sprintf(" %s='%s' ", $key, $value);
			}
			$this -> form[$name]["attr"] = $attr;
		}
	}

	public function setModel($name) {
		$this -> model = M($name);
	}

}
?>