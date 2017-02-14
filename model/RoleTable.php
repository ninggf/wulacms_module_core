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

use wulaphp\db\Table;

class RoleTable extends Table {
	public function users() {
		return $this->belongsToMany('core\model\UserTable', 'user_role');
	}
}