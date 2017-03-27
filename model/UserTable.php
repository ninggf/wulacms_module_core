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

class UserTable extends Table {

	public function roles() {

		return $this->belongsToMany(new RoleTable($this), 'user_role');
	}
}