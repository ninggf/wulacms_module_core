<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\controllers;

use core\model\UserTable;
use dashboard\classes\BackendController;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\io\Ajax;
use wulaphp\io\Response;

class UserController extends BackendController {

	/**
	 *
	 * @return \wulaphp\mvc\view\View
	 */
	public function changePassword() {
		return view();
	}

	public function changePasswordPost($old,$new_password,$confirm_password) {
		$user = new UserTable();
		$rst = $user->get($this->passport->uid);
		if(!Passport::verify($old,$rst['hash'])){
			return Ajax::validate(['old'=>'原密码不正确']);
		}
		if(!$new_password){
			return Ajax::validate(['new_password'=>'密码不能为空']);
		}
		if(strlen($new_password) < 6){
			return Ajax::validate(['new_password'=>'密码长度不足6位']);
		}
		if($new_password != $confirm_password){
			return Ajax::validate(['confirm_password'=>'二次密码不相同']);
		}

		if(!$user->update([
			'hash'=>Passport::passwd($new_password)
		],$this->passport->uid)){
			return Ajax::error('修改失败','notice');

		}


		return Ajax::success('修改成功','notice');
	}

}