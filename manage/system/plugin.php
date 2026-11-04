<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
libload("file.php");
libload("reflection.php");

/**
 * プラグインオブジェクトを取り出す
 * @param string $file
 * @return plugin
 */
function getplugin($file = null) {
    if($file == null) $file = userpath("plugin.ini");
    $obj = session::get('@plugin');
    if($obj == null) {
        $obj = new plugin($file);
        session::set('@plugin', $obj);
    }
    $obj->require_plugin();
    return $obj;
}

/**
 * プラグインの結果
 */
class plugin_result {
    public $html;
    public $element;

    /**
     * コンストラクタ
     */
    public function  __construct($html) {

        $this->html = $html;
    }

    /**
     * データを配列化します
     * @return array
     */
    public function getall(){
        return array('main'=>$this->html) + ((array)$this->element);
    }
}

class plugin {
    var $command = array();
    var $def_act = array();
    var $menu = array();
    var $menuline;
    var $path;

    function  __wakeup() {
    }

    function __construct($path) {
        $this->path = $path;
    }

    /**
     * プラグインを読込みます
     */
    function require_plugin(){
        $conf = new cssConfig();
        $conf->property_double = false;
        $conf->loadFile($this->path);

        //$main = new cssSelector();
        $main = $conf->getSelector('main');

        $this->def_act = $main->getValuesOne("default");
        $this->menu   = $main->getValuesOne("menu");
        $load = $main->getValuesOne("load");
        $load = array_merge($load,$this->def_act);

        foreach($this->menu as $one) {
            if(!preg_match("/^\w+\(/",$one)) $load[] = $one;
        }

        $cmdlist = array();
        foreach($load as $cmd) {
            $path = userpath("$cmd/$cmd.php");
            if(file_exists($path)) require_once ($path);
            $cmdlist[] = $cmd;
        }
        $this->command = $cmdlist;
    }



    /** 登録しているコマンドのauth情報を取り出す :hash(title,values:array(:hash(name,info,key)))の形式 */
    function get_authinfo() {
        $ret = array();
        foreach($this->command as $com) {
            $func = "{$com}_auth";
            $one = ifFunc($func);
            foreach($one as $key=>$value){
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    function plugin_label($title,$type,$force = false) {
        $line = $this->menuline[$type];
        $line = rep($line,'title',$title);
        return array('force'=>$force,'line'=>$line);
    }

    /** メニューを作成する */
    function create_menu() {
        $load = new tLoader(userpath(switchSpTemplate("admin/menu.html")));
        $line  = $load->getData('line');
        $admin = $load->getData('admin');
        $aend = $load->getData('admin_end');
        $user  = $load->getData('user');
        $uend  = $load->getData('user_end');
        $this->menuline = array('admin'=>$admin,'user'=>$user);

		$has_admin = false;
        foreach($this->menu as $mone) {
            if(preg_match("/^\w+\(/",$mone)) {
                $tvar = actFunc($mone,"plugin",$this);
                if($tvar['force']) $menu = $tvar['line'];
                else {
                    $invar = $tvar;
                    continue;
                }
            }
            else {
                $menu = ifFunc($mone . "_menu",$line,$admin,$user, $uend);
            }
            if($menu && $invar) {
                $ret .= $invar['line'];
				$has_admin = true;
                $invar = null;
            }
            $ret .= $menu;
        }
        return $ret . ($has_admin ? $aend : "");
    }


    /**
     * プラグインを実行する
     * @param string $act
     * @return plugin_result
     */
    function action($act = null) {

        if($act) return ifFunc($act . "_act") ;

        #$act = nullのときのみファーストサーチを行う。
        $name = $this->def_act[0];

        if(ifFunc($name . "_first")) {
            return ifFunc($name . "_act");
        }

        #var_dump($this->command);

        #権限ごとに
        foreach($this->command as $cone) {
            if(webauth($cone)){return ifFunc($cone . "_act");}
        }

        foreach($this->command as $cone) {
            if(webauth($cone)){return ifFunc($cone . "_act");}
            if(ifFunc($cone . "_first")){return ifFunc($cone . "_act");}

        }

        $ret = new plugin_result('<font color="red">不正アクセスエラー：権限がありません </font>');
        return $ret;
    }

    /** コマンドが存在するか確認 */
    function exist_command($com) {
        return in_array($com,$this->command);
    }

}


?>