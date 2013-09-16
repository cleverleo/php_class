<?php
abstract class FormCheck {
	protected $_data = null;
	protected $_validate = array();
	private $_err;
	protected $_other = array();

	const DEF = "DEF";

	const REQ = "REQ";
	//必须
	const IN = "in";
	//存在
	const REGEX = "regex";
	//正则表达式
	const CONFIRM = "confirm";
	//判断相等
	const EQUAL = "equal";
	//等于
	const LENGTH = "length";
	//长度
	const BETWEEN = "between";
	//范围
	const FUN = "function";

	const EMAIL = "email";
	//邮箱
	const NUMBER = "number";
	//数字
	const URL = "url";
	//地址
	const INTEGER = "integer";

	const HTML = "html";

	const ERROR = "error";

	const VERIFY = "verify";

	const NOINCLUDE = "noinclude";

	public function check($in) {
		$data = array();

		foreach ($this->_validate as $key => $item) {
			$in[$key] = trim($in[$key]);
			if (!empty($in[$key])) {
				if (is_array($item)) {
					foreach ($item as $key1 => $condition) {
						switch ($key1) {
							case FormCheck::IN :
								if (!in_array($in[$key], $condition)) {
									$this -> _error($item);
									return FALSE;
								};
								break;
							case FormCheck::REGEX :
								if (!preg_match($condition, $in[$key])) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::CONFIRM :
								if ($in[$condition] != $in[$key]) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::EQUAL :
								if ($in[$key] != $condition) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::LENGTH :
								$tmp = explode(",", $condition);
								$length = strlen($in[$key]);
								//包含两端
								if ($tmp[0] > $length || $tmp[1] < $length) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::BETWEEN :
								$tmp = explode(",", $condition);
								//包含两端

								if (!(is_numeric($in[$key]) && $in[$key] >= $tmp[0] && $in[$key] <= $tmp[1])) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::FUN :
								if (!$this -> $condition($in)) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::INTEGER :
								if ((int)$in[$key] != $in[$key]) {
									$this -> _error($item);
									return FALSE;
								}
								break;

							case FormCheck::EMAIL :
								if (!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $in[$key])) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::NUMBER :
								if (!is_numeric($in[$key])) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::URL :
								if (!preg_match("/[a-zA-z]+://[^\s]*/", $in[$key])) {
									$this -> _error($item);
									return FALSE;
								};
								break;

							case FormCheck::VERIFY :
								$tmp = session('verify');
								session('verify', null);
								if ($tmp != md5(strtoupper($in[$key]))) {
									$this -> _error($item);
									return FALSE;
								}
								break;
								DEF:
								break;
						}
					}
				}
				if (!isset($item[FormCheck::NOINCLUDE])) {
					if (isset($item[FormCheck::HTML])) {
						$data[$key] = htmlspecialchars($in[$key]);
					}
					else {
						$data[$key] = $in[$key];
					}
				}
			}
			else {
				if (isset($item[FormCheck::DEF])) {
					$data[$key] = $item[FormCheck::DEF];
				}
				else {
					if (isset($item[FormCheck::REQ])) {
						$this -> _error($item);
						return FALSE;
					}
				}

			}

		}
		$data = array_merge($this -> _other, $data);
		$this -> _data = $this -> beforeReturn($data);
		return TRUE;
	}

	public function getError() {
		return $this -> _err;
	}

	private function _error($value) {
		$this -> _err = empty($value[FormCheck::ERROR]) ? "操作错误" : $value[FormCheck::ERROR];

	}

	public function addData($arr) {
		$this -> _other = $arr;
	}

	protected function beforeReturn($data) {
		return $data;
	}

	public function getData($name = null) {
		if (is_null($name)) {
			return $this -> _data;
		}
		return $this -> _data[$name];
	}

	public function getValidate() {
		return $this -> _validate;
	}

}
?>