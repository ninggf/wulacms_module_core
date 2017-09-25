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

use core\model\UserGridcfgModel;
use dashboard\classes\BackendController;
use wulaphp\app\App;
use wulaphp\io\Ajax;

/**
 * Class GridcfgController
 * @package core\controllers
 * @roles   管理员
 */
class GridcfgController extends BackendController {

	public function index($id, $reload = '') {
		$uid             = $this->passport->uid;
		$columns         = UserGridcfgModel::getColumns($id, $uid);
		$data['columns'] = $columns;
		$data['table']   = $id;
		$data['reload']  = $reload;

		return view($data);
	}

	public function savePost($table, $cols, $ord, $reload = '') {
		$uid          = $this->passport->uid;
		$columns      = UserGridcfgModel::getColumns($table, $uid);
		$data['uid']  = $uid;
		$data['grid'] = $table;
		$colss        = [];
		foreach ($columns as $cid => $v) {
			$colss[ $cid ]['order'] = $ord[ $cid ];
			if (!isset($cols[ $cid ])) {
				$colss[ $cid ]['show'] = 0;
			} else {
				$colss[ $cid ]['show'] = 1;
			}
		}
		$db = App::db();
		if ($db->select()->from('{user_gridcfg}')->where($data)->exist('uid')) {
			$db->update('{user_gridcfg}')->set(['columns' => json_encode($colss)])->where($data)->exec();
		} else {
			$data['columns'] = json_encode($colss);
			$db->insert($data)->into('{user_gridcfg}')->exec();
		}

		return Ajax::reload($reload ? $reload : 'document', '表格配置完成');
	}
}