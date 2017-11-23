<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\form;

use core\model\UserTable;

class AdminForm extends UserTable {
	public $table = 'user';
	/**
	 * 启用
	 * @var \wula\ui\classes\CheckboxField
	 * @type bool
	 * @layout 1,col-xs-12
	 */
	public $status = 1;
	/**
	 * 密码(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\PasswordField
	 * @type string
	 * @required
	 * @minlength (8)
	 * @passwd (3) => 必须由大、小写字母，符号，数字组成
	 * @layout 4,col-xs-6
	 */
	public $password;
	/**
	 * 确认密码
	 * @var \wula\ui\classes\PasswordField
	 * @type string
	 * @equalTo (#password)
	 * @layout 4,col-xs-6
	 */
	public $password1;
	/**
	 * 角色
	 * @var \wula\ui\classes\MultipleCheckboxFiled
	 * @type []
	 * @layout   5,col-xs-12
	 * @required 至少要选一个角色
	 * @option {"inline":1}
	 * @see      \wulaphp\form\providor\TableDataProvidor
	 * @dsCfg {"table":"role","orderBy":["id","asc"]}
	 */
	public $roles;

	public function alterFieldOptions($name, &$options) {
		if ($name == 'password' && $this->_tableData) {
			$options['label'] = '密码';
		}
	}

	protected function beforeCreateWidgets() {
		if ($this->_tableData && $this->_tableData['id'] == '1') {
			$this->excludeFields('roles', 'status');
		}
	}
}