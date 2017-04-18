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

use dashboard\classes\BackendController;
use wula\form\UiTable;
use wula\form\UiTableData;

class ModuleController extends BackendController {
	/**
	 * @return \wulaphp\mvc\view\SmartyView
	 * @acl m:system/module
	 */
	public function installed() {
		$uitable    = new UiTable('~core/module/data/installed');
		$uitable[0] = ['key' => 'id', 'title' => 'ID', 'sortable' => 'custom'];
		$uitable[1] = ['key' => 'name', 'title' => '名称', 'sortable' => 'custome'];

		return view(['table' => $uitable]);
	}

	public function data($type = 'installed', $page, $filter, $sort) {
		if ($page['current'] > 1) {
			return new UiTableData([['id' => 2, 'name' => 'bb']]);
		}
		$data                    = new UiTableData([['id' => 2, 'name' => 'bb']]);
		$data->permits['delete'] = $this->passport->cando('d:system/module');

		return $data;
	}
}