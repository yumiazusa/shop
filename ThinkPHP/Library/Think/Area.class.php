<?php
namespace Think;
class Area{
	public static function city($pro){
		$city = json_encode($pro);
	
		$str="";
		if(!defined('CALENDAR_INIT')){
			define('CALENDAR_INIT',1);
			$str.='<select id="province" name="province" class="span2">
                         <option></option>
                   </select>
                    <select id="city" name="city" class="span2">
                               <option></option>
                    </select>
                    <select id="area" name="area" class="span2">
                        <option></option>
                    </select>
					<script type="text/javascript"  src="'.__ROOT__.'/Public/Admin/js/area.js"></script>
					<script type="text/javascript">_init_area('.$city.');</script>';
		}
		return $str;
    }
}
?>
