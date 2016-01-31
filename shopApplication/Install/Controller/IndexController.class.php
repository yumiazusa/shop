<?php
namespace Install\Controller;
use Think\Controller;
class IndexController extends Controller {

    public $config = '';                                                        // 相关配置
    public $model = '';                                                         // 实例化一个model
    public $content;                                                            // 内容
    public $dbName = '';                                                        // 数据库名
    public $dir_sep = '/';                                                      // 路径符号
    private $db_host;
    private $db_user;
    private $db_name;
    private $db_pwd;
    private $db_prefix;
    private $link;


    // 获取安装首页界面
    public function index(){
       $this->display('index');
    }

    // 检查是否已生成锁文件
    public function checkBeInstalled ()
    {
        $lockFile = APP_PATH . 'Common/lock.locked';
        if (file_exists ($lockFile))
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    // 检查安装环境
    public function step_2()
    {
        $getAgree = I('get.agree');
        if ($getAgree != 'yes')
        {
            $this->redirect('index');
            return FALSE;
        }
        // 系统文件
        $dirPath = array(
            0 => 'Public',
            1 => 'Public/Adv',
            2 => 'Public/Backup',
            3 => 'Public/download',
            4 => 'Public/download/uploadFile',
            5 => 'Public/Links',
            6 => 'Public/News',
            7 => 'Public/Product',
            8 => 'Public/Product/adv',
            9 => 'Public/Product/feacher',
            10 => 'Public/Product/param',
            11 => 'Public/Product/thumb',
            12 => 'Public/Service',
            13 => 'Public/Uploads',
            14 => 'Public/Home/move',
            15 => 'Public/Home/move/simg',
            16 => 'Public/Home/move/bimg',
            17 => 'Public/User',
            18 => 'Application',
            19 => 'Application/Common',
            20 => 'Application',
            21 => 'Application/Admin',
            22 => 'Application/Admin/Conf',
            23 => 'Application/Home',
            24 => 'Application/Home/Conf',
        );

        // 目录权限检查
        $pathArr = array();
        foreach ($dirPath as $key=>$val)
        {
            if (file_exists($val))
            {
                $pathArr[$key]['fname'] = $val;
                if (is_readable($val))
                {
                    $pathArr[$key]['r'] = 1;
                }
                else
                {
                    $pathArr[$key]['r'] = 0;
                }

                if (is_writeable($val))
                {
                    $pathArr[$key]['w'] = 1;
                }
                else
                {
                    $pathArr[$key]['w'] = 0;
                }
            }
        }

        // 检查系统环境
        $config['web_server']   = I('server.SERVER_SOFTWARE');
        $config['os']           = PHP_OS;
        $config['max_upload']   = ini_get('file_uploads') ? ini_get('upload_max_filesize') : '不支持';
        $config['now_time']     = date('Y-m-d H:i:s');

        $this->assign('config', $config);
        $this->assign('pathInfo', $pathArr);
        $this->display('step2');
    }

    // 数据库和管理员
    public function step_3() {
        $this->display('step3');
    }

    public function step_4 ()
    {
		$db = array();
        $db['DB_HOST']      = I('post.DB_HOST');
        $db['DB_USER']      = I('post.DB_USER');
        $db['DB_PWD']       = I('post.DB_PWD');
        $db['DB_NAME']      = I('post.DB_NAME');
        $db['DB_PREFIX']    = I('post.DB_PREFIX');

        // 将数据库信息写入配置文件
        $filePut = $this->_saveConfig($db);
        if ($filePut == false)
        {
            $this->error('文件读写权限不够');
            exit;
        }

        // 连通性测试
        $dbLink = $this->_checkLink($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD']);
        if ( ! $dbLink)
        {
            $this->error('对不起，无法建立与数据库的连接');
            exit;
        }

        // 检查数据库
        $checkDB = $this->_createDB($db['DB_NAME']);
        if ( ! $checkDB)
        {
            $this->error('对不起，数据库创建失败');
            exit;
        }

        // 获取数据对象
        $this->config = array(
            'path' => APP_PATH . 'Install/Common/data',                             // 备份文件存在哪里
            'isCompress' => 0,                                                  // 是否开启gzip压缩      【未测试】
            'isDownload' => 0                                                   // 备份完成后是否下载文件 【未测试】
        );

		C('DB_NAME', $db['DB_NAME']);
		C('DB_PWD', $db['DB_PWD']);
		C('DB_HOST', $db['DB_HOST']);
		C('DB_USER', $db['DB_USER']);
		C('DB_PREFIX', $db['DB_PREFIX']);
		$this->dbName = $db['DB_NAME'];
		
        // exit;
        $this->model = M();                                                 // 实例化一个模型对象

        // =========================================================

        if ( ! $this->recover("data.sql"))
        {
            $this->error('数据导入失败');
            exit;
        }

        // 修改表前缀
        $list = $this->model->query("SHOW TABLE STATUS FROM {$this->dbName}");

        foreach ($list as $key => $val)
        {
            $tabName = $val['name'];
            $newTabName = str_replace('blue_', $db['DB_PREFIX'], $tabName);
            $sql = "RENAME TABLE `{$db['DB_NAME']}`.`{$tabName}` TO `{$db['DB_NAME']}`.`{$newTabName}`";
            $this->model->execute($sql);
        }

        // 用户
        $data['role_id']        = 1;
        $data['username']       = I('username');
        $data['password']       = sha1($_POST['password']);
        $data['create_time']    = time();

        $modelAdminUsers = M('admin_users');
        if ($modelAdminUsers->create())
        {
            // 用户信息插入
            $lastId = $modelAdminUsers->data($data)->add();
            $dataInfo['uid'] = $lastId;
            $modelInfo = M('admin_info');
            $modelInfo->data($dataInfo)->add();

            // 用户组权限数据插入：超级管理员
            $groupInfo['uid'] = $lastId;
            $groupInfo['group_id'] = 1;
            $modelGroup = M('auth_group_access');
            $modelGroup->data($groupInfo)->add();
        }

        if ($lastId)
        {
            // 生成锁文件
            $lockFile = APP_PATH . 'Common/lock.locked';
            touch($lockFile);
            chmod($lockFile, 0766);
            file_put_contents($lockFile, 'ok');

            $this->redirect('step_5');
            exit;
        }

    }

    public function step_5 ()
    {
        $this->display('step5');
    }


    private function _saveConfig ($db)
    {
        $filePut = '';
        $homeConfigPath = APP_PATH . 'Home/Conf/config.php';
        $adminConfigPath = APP_PATH . 'Admin/Conf/config.php';
        $installConfigPath = APP_PATH . 'Install/Conf/config.php';

        if ( ! file_exists($homeConfigPath) || ! file_exists($adminConfigPath))
        {
            $this->error('应用配置文件不存在，请检查源码包完整性');
            exit;
        }
        else
        {
            // 正则替换掉里面的内容
            $pattern = array();
            $replacement = array();
            foreach ($db as $key=>$val)
            {
                $pattern[] = "/'{$key}'\s*=>\s*'(.*?)'\s*,/";
                $replacement[] = "'{$key}' => '{$val}',";
            }
            // 写入install目录下的文件
            $content = file_get_contents($installConfigPath);
            $getConfigText = preg_replace($pattern, $replacement, $content);
            $filePut = file_put_contents($installConfigPath, $getConfigText);
            // 写入Home目录下的文件
            $content = file_get_contents($homeConfigPath);
            $getConfigText = preg_replace($pattern, $replacement, $content);
            $filePut = file_put_contents($homeConfigPath, $getConfigText);
            // 写入admin目录下的文件
            $content = file_get_contents($adminConfigPath);
            $getConfigText = preg_replace($pattern, $replacement, $content);
            $filePut = file_put_contents($adminConfigPath, $getConfigText);
        }
        return $filePut;
    }

    /**
     * 创建数据库，如果存在不处理，如果不存在，创建之
     * @param  string $dbName 新数据库名
     * @return boolean        创建成功返回真，失败返回假
     */
    private function _createDB($dbName)
    {
        if ( @mysql_select_db($dbName, $this->link))
        {
            return TRUE;
        }
        mysql_set_charset("UTF8");
        $createDB = mysql_query(" CREATE DATABASE `{$dbName}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
        if ($createDB)
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 测试与数据库连通性
     * @param  string $host     主机名
     * @param  string $username 用户名
     * @param  string $password 密码
     * @return mixed            成功返回资源型，失败返回FALSE
     */
    private function _checkLink ($host, $username, $password)
    {
        $link = @mysql_connect($host, $username, $password);
        if ($link)
        {
            $this->link = $link;
            $this->db_host = $host;
            $this->db_user = $username;
            $this->db_pwd  = $password;
            // @mysql_close($link);
        }
        return $link;
    }

    // ==================================================


    //初始化数据
    public function __construct() {
        parent::__construct();
        header("Content-type: text/html;charset=utf-8");
        set_time_limit(0);                                                      // 不超时
        ini_set('memory_limit','500M');
        
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 获取数据表
     * +------------------------------------------------------------------------
     */
    function fileList() {
        $path = $this->config['path'];
        $fileArr = $this->MyScandir($path);
        foreach ($fileArr as $key => $value) {
            if ($key > 1) {
                //获取文件创建时间
                $fileTime = date('Y-m-d H:i:s', filemtime($path . '/' . $value));
                $fileSize = filesize($path . '/' . $value) / 1024;
                //获取文件大小
                $fileSize = $fileSize < 1024 ? number_format($fileSize, 2) . ' KB' :
                        number_format($fileSize / 1024, 2) . ' MB';
                //构建列表数组
                $list[] = array(
                    'name' => $value,
                    'time' => $fileTime,
                    'size' => $fileSize
                );
            }
        }

        $getCounts = count($list);
    }


    //还原数据库
    function recover($fileName) {
        if ($this->recover_file($fileName)) {
            return true;
        } else {
            return false;
        }
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 下载备份文件
     * +------------------------------------------------------------------------
     */
    function downloadBak() {
        $file_name = $_GET['file'] . '.sql';
        $file_dir = $this->config['path'];
        if (!file_exists($file_dir . "/" . $file_name)) { //检查文件是否存在
            return false;
            exit;
        } else {
            $file = fopen($file_dir . "/" . $file_name, "r"); // 打开文件
            // 输入文件标签
            header('Content-Encoding: none');
            header("Content-type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Accept-Length: " . filesize($file_dir . "/" . $file_name));
            header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename=" . $file_name);  //以真实文件名提供给浏览器下载
            header('Pragma: no-cache');
            header('Expires: 0');
            //输出文件内容
            echo fread($file, filesize($file_dir . "/" . $file_name));
            fclose($file);
            exit;
        }
    }
        
    /* -
     * +------------------------------------------------------------------------
     * * @ 获取 目录下文件数组
     * +------------------------------------------------------------------------
     * * @ $FilePath 目录路径
     * * @ $Order    排序
     * +------------------------------------------------------------------------
     * * @ 获取指定目录下的文件列表，返回数组
     * +------------------------------------------------------------------------
     */
    private function MyScandir($FilePath = './', $Order = 0) {
        $FilePath = opendir($FilePath);
        while ($filename = readdir($FilePath)) {
            $fileArr[] = $filename;
        }
        $Order == 0 ? sort($fileArr) : rsort($fileArr);
        return $fileArr;
    }

    /********************************************************************************************* */
    /* -
     * +------------------------------------------------------------------------
     * * @ 读取备份文件
     * +------------------------------------------------------------------------
     * * @ $fileName 文件名
     * +------------------------------------------------------------------------
     */
    private function getFile($fileName) {
        $this->content = '';
        $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $fileName);
        if (is_file($fileName)) {
            $ext = strrchr($fileName, '.');
            if ($ext == '.sql') {
                $this->content = file_get_contents($fileName);
            } elseif ($ext == '.gz') {
                $this->content = implode('', gzfile($fileName));
            } else {
                $this->error('无法识别的文件格式!');
            }
        } else {
            $this->error('文件不存在!');
        }
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 把数据写入磁盘
     * +------------------------------------------------------------------------
     */
    private function setFile() {
        $recognize = '';
        $recognize = $this->dbName;
        $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $recognize . '_' . date('YmdHis') . '_' . mt_rand(100, 999) . '.sql');
        $path = $this->setPath($fileName);
        if ($path !== true) {
            $this->error("无法创建备份目录目录 '$path'");
        }
        if ($this->config['isCompress'] == 0) {
            if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
                $this->error('写入文件失败,请检查磁盘空间或者权限!');
            }
        } else {
            if (function_exists('gzwrite')) {
                $fileName .= '.gz';
                if ($gz = gzopen($fileName, 'wb')) {
                    gzwrite($gz, $this->content);
                    gzclose($gz);
                } else {
                    $this->error('写入文件失败,请检查磁盘空间或者权限!');
                }
            } else {
                $this->error('没有开启gzip扩展!');
            }
        }
        if ($this->config['isDownload']) {
            $this->downloadFile($fileName);
        }
    }

    private function trimPath($path) {
        return str_replace(array('/', '\\', '//', '\\\\'), $this->dir_sep, $path);
    }

    private function setPath($fileName) {
        $dirs = explode($this->dir_sep, dirname($fileName));
        $tmp = '';
        foreach ($dirs as $dir) {
            $tmp .= $dir . $this->dir_sep;
            if (!file_exists($tmp) && !@mkdir($tmp, 0777, true))
                return $tmp;
        }
        return true;
    }

    //未测试
    private function downloadFile($fileName) {
        ob_end_clean();
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($fileName));
        header('Content-Disposition: attachment; filename=' . basename($fileName));
        readfile($fileName);
    }
    /* -
     * +------------------------------------------------------------------------
     * * @ 给字符串添加 ` `
     * +------------------------------------------------------------------------
     * * @ $str 字符串
     * +------------------------------------------------------------------------
     * * @ 返回 `$str`
     * +------------------------------------------------------------------------
     */
    private function backquote($str) {
        return "`{$str}`";
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 获取数据库的所有表
     * +------------------------------------------------------------------------
     * * @ $dbName  数据库名称
     * +------------------------------------------------------------------------
     */
    private function getTables($dbName = '') {
        if (!empty($dbName)) {
            $sql = 'SHOW TABLES FROM ' . $dbName;
        } else {
            $sql = 'SHOW TABLES ';
        }
        $result = $this->model->query($sql);
        $info = array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 把传过来的数据 按指定长度分割成数组
     * +------------------------------------------------------------------------
     * * @ $array 要分割的数据
     * * @ $byte  要分割的长度
     * +------------------------------------------------------------------------
     * * @ 把数组按指定长度分割,并返回分割后的数组
     * +------------------------------------------------------------------------
     */
    private function chunkArrayByByte($array, $byte = 5120) {
        $i = 0;
        $sum = 0;
        $return = array();
        foreach ($array as $v) {
            $sum += strlen($v);
            if ($sum < $byte) {
                $return[$i][] = $v;
            } elseif ($sum == $byte) {
                $return[++$i][] = $v;
                $sum = 0;
            } else {
                $return[++$i][] = $v;
                $i++;
                $sum = 0;
            }
        }
        return $return;
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 备份数据 { 备份每张表、视图及数据 }
     * +------------------------------------------------------------------------
     * * @ $tables 需要备份的表数组
     * +------------------------------------------------------------------------
     */
    private function backup($tables) {
        if (empty($tables))
        {
            $this->error('没有需要备份的数据表!');
        }
        $this->content = '/* This file is created by MySQLReback ' . date('Y-m-d H:i:s') . ' */';
        foreach ($tables as $i => $table) {
            $table = $this->backquote($table);                            //为表名增加 ``
            $tableRs = $this->model->query("SHOW CREATE TABLE {$table}");       //获取当前表的创建语句
            if (!empty($tableRs[0]["create view"])) {
                $this->content .= "\r\n /* 创建视图结构 {$table}  */";
                $this->content .= "\r\n DROP VIEW IF EXISTS {$table};/* MySQLReback Separation */ " . $tableRs[0]["create view"] . ";/* MySQLReback Separation */";
            }
            if (!empty($tableRs[0]["create table"])) {
                $this->content .= "\r\n /* 创建表结构 {$table}  */";
                $this->content .= "\r\n DROP TABLE IF EXISTS {$table};/* MySQLReback Separation */ " . $tableRs[0]["create table"] . ";/* MySQLReback Separation */";
                $tableDateRow = $this->model->query("SELECT * FROM {$table}");
                $valuesArr = array();
                $values = '';
                if (false != $tableDateRow) {
                    foreach ($tableDateRow as &$y) {
                        foreach ($y as &$v) {
                           if ($v=='')                                  //纠正empty 为0的时候  返回tree
                                $v = 'null';                                    //为空设为null
                            else
                                $v = "'" . mysql_escape_string($v) . "'";       //非空 加转意符
                        }
                        $valuesArr[] = '(' . implode(',', $y) . ')';
                    }
                }
                $temp = $this->chunkArrayByByte($valuesArr);
                if (is_array($temp)) {
                    foreach ($temp as $v) {
                        $values = implode(',', $v) . ';/* MySQLReback Separation */';
                        if ($values != ';/* MySQLReback Separation */') {
                            $this->content .= "\r\n /* 插入数据 {$table} */";
                            $this->content .= "\r\n INSERT INTO {$table} VALUES {$values}";
                        }
                    }
                }
                 //                dump($this->content);
                 //                exit;
            }
        }
        if (!empty($this->content)) {
            $this->setFile();
        }
        return true;
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 还原数据
     * +------------------------------------------------------------------------
     * * @ $fileName 文件名
     * +------------------------------------------------------------------------
     */
    private function recover_file($fileName) {
        $this->getFile($fileName);
        if (!empty($this->content)) {
            $content = explode(';/* MySQLReback Separation */', $this->content);
            foreach ($content as $i => $sql) {
                $sql = trim($sql);
                if (!empty($sql)) {
                    $mes = $this->model->execute($sql);
                    if (false === $mes) {                                       //如果 null 写入失败，换成 ''
                        $table_change = array('null' => '\'\'');
                        $sql = strtr($sql, $table_change);
                        $mes = $this->model->execute($sql);
                    }
                    if (false === $mes) {                                       //如果遇到错误、记录错误
                        $log_text = '以下代码还原遇到问题:';
                        $log_text.="\r\n $sql";
                        set_log($log_text);
                    }
                }
            }
        } else {
            $this->error('无法读取备份文件!');
        }
        return true;
    }






}