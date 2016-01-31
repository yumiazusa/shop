<?php

namespace   Admin\Widget;
use         Think\Controller;

class TopInfoWidget extends Controller {

    public function userInfo () {
        $this->display('Public:userInfo');
    }

    public function messageInfo () {
        $this->display('Public:messageInfo');
    }
}