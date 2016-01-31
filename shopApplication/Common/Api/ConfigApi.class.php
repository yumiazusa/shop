<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class ConfigApi {
	/**
	 * 获取数据库中的配置列表
	 * @return array 配置数组
	 */
	public static function lists() {
		if (F('DB_CONFIG_DATA')) {
			$config = F('DB_CONFIG_DATA');
		} else {
			$map = array('status' => 1);
			$data = M('sys_config')->field('cname,cvalue')->select();

			$config = array();
			if ($data && is_array($data)) {
				foreach ($data as $value) {
					$config[$value['cname']] = self::parse(3, $value['value']);
				}
			}
			F('DB_CONFIG_DATA', $config);

		}

		return $config;
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 */
	private static function parse($type, $value) {
		switch ($type) {
		case 3: //解析数组
			$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
			if (strpos($value, ':')) {
				$value = array();
				foreach ($array as $val) {
					list($k, $v) = explode(':', $val);
					$value[$k] = $v;
				}
			} else {
				$value = $array;
			}
			break;
		}
		return $value;
	}
}