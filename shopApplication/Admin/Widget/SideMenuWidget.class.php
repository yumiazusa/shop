<?php

namespace   Admin\Widget;
use         Think\Controller;

class SideMenuWidget extends Controller {


    public function sideMenu() {
        $this->display('Public:sideMenu');
    }

}