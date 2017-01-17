<?php
/*
 * admin类型的通行证.
 *
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\classes;

use wulaphp\auth\Passport;

class AdminPassport extends Passport {

	public function is($roles) {
		return true;
	}

	protected function checkAcl($res, $extra) {
		return true;
	}

	protected function doAuth($data = null) {
		return true;
	}
}