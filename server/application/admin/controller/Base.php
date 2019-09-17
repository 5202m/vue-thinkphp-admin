<?php
namespace app\admin\controller;

use app\admin\model\AccessLogs;
use think\Request;
use think\Controller;

class Base extends Controller
{
    private $param = [];

    public function _initialize()
    {
        parent::_initialize();
        // header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, sessionId, X-Requested-Token");
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function index()
    {
        echo "你好";
    }

    public function login()
    {
        $username = $this->param['username'];
        $password = $this->param['password'];
        $user = new \app\admin\model\AdminUser();
        $menu = new \app\admin\model\Menu();
        //$user = model('AdminUser');
        //$menu = model('Menu');
        $data = [
          'username' => $username,
          'password' => md5($password.$username)
        ];
        $ret  = $user->getUserLogin($data);
        if ($ret) {

            //记录登录日志  ---begin
            $ip = $this->request->ip();
            $url='http://freeapi.ipip.net/'.$ip;
            $result = json_decode(curl_get($url), true);
            if ($result) {
                $res = [
                'ip'        => $ip,
                'country'   => $result[0],
                'region'    => $result[1],
                'city'      => $result[2],
                'isp'       => $result[4],
                'create_at' =>time(),
              ];
                $accessLogs = new AccessLogs();
                $accessLogs->saveLogs($res);
                //model('AccessLogs')->saveLogs($res);
            }
            // ---end

            unset($ret['password']);
            $token = md5(microtime());

            if ($ret['id'] == 1) {
                $m = getLoginTree($menu->getMenus(['status'=>1]));
            } else {
                $rule = new \app\admin\model\Rule();
                //$rule = model('Rule');
                $r = $rule->getRuleById($ret['r_id']);
                if ($r) {
                    $ids = explode(',', $r['rs']);
                    $ret['rules'] = getRules($menu->getMenuByIds($ids));
                    $m = getLoginTree($menu->getMenuByIds($ids));
                } else {
                    $m = [];
                }
            }
            cache($token, json_encode($ret), 3600);
            $data = [
              'token' => $token,
              'user'  => $ret,
              'menus' => $m
            ];
            $d['last_login_at'] = time();
            $ret = $user->updateUser($ret['id'], $d, false);
            return msg(200, $data);
        } else {
            return msg(100, null, $user->getError());
        }
    }
}
