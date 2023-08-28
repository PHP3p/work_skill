<?php
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', ROUTE_M, 0);

class index extends common {

    /**
     * 管理员后台
     */
    public function init() {
        debug();
        $total = D('guestbook')->field('id')->where(array('replyid'=>'0','isread'=>'0'))->total(); //未读消息
        include $this->admin_tpl('index');
    }


    /**
     * 管理员登录
     */
    public function login() {
        if(isset($_POST['dosubmit'])) {
            if(empty($_SESSION['code']) || strtolower($_POST['code'])!=$_SESSION['code']){
                $_SESSION['code'] = '';
                showmsg(L('code_error'), '', 1);
            }
            $_SESSION['code'] = '';
            if(!is_username($_POST['username'])) showmsg(L('user_name_format_error'));
            if(!is_password($_POST['password'])) showmsg(L('password_format_error'));
            M('admin')->check_admin($_POST['username'], password($_POST['password']));
        }else{
            $this->_login();
        }
    }


    /**
     * 管理员退出
     */
    public function public_logout() {
        unset($_SESSION['adminid'], $_SESSION['adminname'], $_SESSION['roleid'], $_SESSION['admininfo']);
        del_cookie('adminid');
        del_cookie('adminname');
        showmsg(L('you_have_safe_exit'), U('login'), 1);
    }


    /**
     * 管理员公共桌面
     */
    public function public_home() {
        yzm_base::load_common('lib/update'.EXT, 'admin');
        if(isset($_GET['up'])){update::check();}

        $tpl = APP_PATH.'admin'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'public_home.html';
        if(!is_file($tpl)) $this->_force_logout();
        $html = file_get_contents($tpl);
        if(!strpos($html, 'YzmCMS') || !strpos($html, 'www.yzmcms.com')){
            $this->_force_logout();
        }
        // 统计信息
        $count = array();
        $count[] = D('article')->total();
        $count[] = D('module')->total();
        $count[] = D('member')->total();
        $count[] = D('admin')->total();

        ob_start();
        include $this->admin_tpl('public_home');
        $data = ob_get_contents();
        ob_end_clean();
        system_information($data);
    }


    /**
     * 清除错误日志
     */
    public function public_clear_log() {
        if($_SESSION['roleid'] != 1) return_json(array('status'=>0,'message'=>'此操作仅限于超级管理员！'));
        if(is_file(YZMPHP_PATH.'cache/error_log.php')){
            $res = @unlink(YZMPHP_PATH.'cache/error_log.php');
            if(!$res) return_json(array('status'=>0,'message'=>L('operation_failure')));
            D('admin_log')->insert(array('module'=>ROUTE_M,'action'=>ROUTE_C,'adminname'=>$_SESSION['adminname'],'adminid'=>$_SESSION['adminid'],'querystring'=>'清除错误日志','logtime'=>SYS_TIME,'ip'=>self::$ip));
        }
        return_json(array('status'=>1,'message'=>L('operation_success')));
    }


    private function _force_logout(){
        session_destroy();
        echo '<script type="text/javascript">window.top.location="'.U('login').'"</script>';
        exit();
    }


    private function _login(){
        ob_start();
        include $this->admin_tpl('login');
        $data = ob_get_contents();
        ob_end_clean();
        echo $data.base64_decode('PCEtLSBQb3dlcmVkIEJ5IFl6bUNNU+WGheWuueeuoeeQhuezu+e7nyAgLS0+');
    }
}
