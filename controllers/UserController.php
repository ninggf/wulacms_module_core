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

use core\form\ChangePasswordForm;
use core\model\UserTable;
use dashboard\classes\BackendController;
use wula\ui\classes\BootstrapFormRender;
use wulaphp\auth\Passport;
use wulaphp\io\Ajax;
use wulaphp\validator\JQueryValidatorController;
use wulaphp\validator\ValidateException;

/**
 * Class UserController
 * @accept core\model\UserTable
 */
class UserController extends BackendController {
	use JQueryValidatorController;

	/**
	 * 我的账户页面.
	 * @return \wulaphp\mvc\view\View
	 */
	public function account() {
		$form        = new UserTable();
		$data['uid'] = $this->passport->uid;
		$form->inflateByData($this->passport->info());
		$data['form']  = BootstrapFormRender::v($form);
		$data['rules'] = $form->encodeValidatorRule($this);

		$pwdForm          = new ChangePasswordForm();
		$data['pwdform']  = BootstrapFormRender::v($pwdForm);
		$data['pwdrules'] = $pwdForm->encodeValidatorRule($this);

		return view($data);
	}

	/**
	 * 修改账户信息.
	 * @return \wulaphp\mvc\view\JsonView
	 */
	public function accountPost() {
		$form = new UserTable();
		$data = $form->inflate();
		$id   = intval($data['id']);
		if (empty($id)) {
			return Ajax::error('未知账户');
		} else if ($id != $this->passport->uid) {
			return Ajax::error('你无权修改此账户信息');
		}

		try {
			$rst = $form->updateAccount($data);
			if ($rst) {
				$this->passport->username = $data['username'];
				$this->passport->nickname = $data['nickname'];
				$this->passport->phone    = $data['phone'];
				$this->passport->email    = $data['email'];
				$this->passport->store();

				return Ajax::reload('document', '账户信息更新成功');
			}

			return Ajax::error($form->lastError());
		} catch (ValidateException $ve) {
			return Ajax::validate('UserTable', $ve->getErrors());
		} catch (\PDOException $pe) {
			return Ajax::error($pe->getMessage());
		}
	}

	/**
	 * 修改密码
	 * @return \wulaphp\mvc\view\JsonView
	 */
	public function chpwdPost() {
		$data = rqsts(['id', 'newpwd', 'newpwd1', 'oldpwd']);
		$user = new UserTable();
		$rst  = $user->get($this->passport->uid);
		if (!Passport::verify($data['oldpwd'], $rst['hash'])) {
			return Ajax::validate('ChPwdForm', ['oldpwd' => '原密码不正确']);
		}
		$new_password = $data['newpwd'];
		if (!$new_password) {
			return Ajax::validate('ChPwdForm', ['newpwd' => '新的密码不能为空']);
		}
		if (strlen($new_password) < 6) {
			return Ajax::validate('ChPwdForm', ['newpwd' => '密码长度不足6位']);
		}
		$confirm_password = $data['newpwd1'];
		if ($new_password != $confirm_password) {
			return Ajax::validate('ChPwdForm', ['newpwd' => '二次密码不相同']);
		}
		try {
			if ($user->chagnePassword($this->passport->uid, $new_password)) {
				return Ajax::success('密码已修改');
			}

			return Ajax::error('密码修改失败');
		} catch (\Exception $e) {
			return Ajax::error($e->getMessage());
		}
	}
}