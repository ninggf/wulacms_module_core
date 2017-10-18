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

use wulaphp\form\FormTable;
use wulaphp\validator\JQueryValidator;

class ChangePasswordForm extends FormTable {
	use JQueryValidator;
	public $table = 'user';
	/**
	 * @type int
	 */
	public $id;
	/**
	 * 原始密码(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\PasswordField
	 * @type string
	 * @required
	 * @layout 1,col-xs-12 col-md-4
	 */
	public $oldpwd;
	/**
	 * 新的密码(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\PasswordField
	 * @type string
	 * @required
	 * @minlength (8)
	 * @password (3) => 必须由大、小写字母，符号，数字组成
	 * @layout 2,col-xs-12 col-md-4
	 */
	public $newpwd;
	/**
	 * 确认密码(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\PasswordField
	 * @type string
	 * @equalTo (#newpwd)
	 * @layout 2,col-xs-12 col-md-4
	 */
	public $newpwd1;
}