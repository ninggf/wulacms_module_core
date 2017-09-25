<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\model;

use wulaphp\app\App;
use wulaphp\db\Table;
use wulaphp\util\ArrayCompare;

class UserGridcfgModel extends Table {
	public static $passportType = 'admin';

	public static function echoSetButton($id, $reload) {
		$url = App::url('~core/gridcfg/') . urlencode($id) . '/' . urlencode($reload);

		return '<a href="' . $url . '" data-ajax="dialog" data-dialog-id="gridcfg.dialog" data-dialog-title="表格设置" data-dialog-width="400px" data-dialog-icon="fa fa-th-list"><i class="fa fa-th-list"></i></a>';
	}

	public static function echoHead($id) {
		$user    = whoami(self::$passportType);
		$uid     = $user->uid;
		$columns = self::getColumns($id, $uid);
		$heads   = [];

		foreach ($columns as $cid => $col) {
			if ($col['show']) {
				$align   = isset($col['align']) ? ' class="text-' . $col['align'] . '" ' : '';
				$heads[] = @'<th' . $align . ' width="' . $col['width'] . '"' . ($col['sort'] ? 'data-sort="' . $col['sort'] . '"' : '') . '>' . $col['name'] . '</th>';
			}
		}

		return implode('', $heads);
	}

	public static function echoRow($id, $data, $extras = []) {
		static $columns = [];
		if (!isset($columns[ $id ])) {
			$user           = whoami(self::$passportType);
			$uid            = $user->uid;
			$column         = self::getColumns($id, $uid);
			$columns[ $id ] = $column;
		}
		$rows = [];
		foreach ($columns[ $id ] as $cid => $col) {
			if ($col['show']) {
				$align  = isset($col['align']) ? ' class="text-' . $col['align'] . '" ' : '';
				$rows[] = '<td' . $align . '>' . (is_callable($col['render']) ? @call_user_func_array($col['render'], [
						$data[ $cid ],
						$data,
						$extras
					]) : $data[ $cid ]) . '</td>';
			}
		}

		return implode('', $rows);
	}

	public static function colspan($id, $num) {
		static $columns = [];
		if (!isset($columns[ $id ])) {
			$user   = whoami(self::$passportType);
			$uid    = $user->uid;
			$column = self::getColumns($id, $uid);

			foreach ($column as $cid => $col) {
				if ($col['show']) {
					$num += 1;
				}
			}

			$columns[ $id ] = $num;
		}

		return $columns[ $id ];
	}

	/**
	 * @param string $id  表格编号
	 * @param int    $uid 用户ID
	 *
	 * @return array
	 */
	public static function getColumns($id, $uid) {
		static $gcolumns = [];
		if (isset($gcolumns[ $id ][ $uid ])) {
			return $gcolumns[ $id ][ $uid ];
		}

		$cols = App::db()->select('*')->from('{user_gridcfg}')->where(['uid' => $uid, 'grid' => $id])->get('columns');

		$columns = apply_filter('get_columns_of_' . $id, []);

		if ($cols) {
			$cols = @json_decode($cols, true);
			foreach ($columns as $col => $cfg) {
				if (isset($cols[ $col ]) && $cols[ $col ]['show']) {
					$columns[ $col ]['show'] = true;
				} else {
					$columns[ $col ]['show'] = false;
				}
				if (isset($cols[ $col ])) {
					$columns[ $col ]['order'] = $cols[ $col ]['order'];
				}
			}
		}

		uasort($columns, ArrayCompare::compare('order'));
		$gcolumns[ $id ][ $uid ] = $columns;

		return $columns;
	}
}