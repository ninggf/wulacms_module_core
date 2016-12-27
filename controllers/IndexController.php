<?php

namespace core\controllers;

use wulaphp\mvc\controller\AdminController;

/**
 * 默认控制器.
 */
class IndexController extends AdminController {
	/**
	 * 默认控制方法.
	 */
	public function index() {
		$data = ['module' => 'Index'];

		// 你的代码写在这里

		return view($data);
	}
}