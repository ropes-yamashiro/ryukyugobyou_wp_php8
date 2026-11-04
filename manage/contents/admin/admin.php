<?php

/**
 * 管理画面を作成する
 * @param array $userinfo 個人名
 * @param string $menu メニュー
 * @param plugin_result $contents 追加項目
 * @return string
 */
function admin_view($userinfo,$menu,$contents) {
    $html_page = userread(switchSpTemplate("admin/top-container.html"));

    #個人情報
    $userinfo = $userinfo + array('userid'=>null,'username'=>null);
    $html_page = phash($userinfo,$html_page);

    #サイト情報
    $info = siteini('admin');
    $html_page = phash($info,$html_page);


    #メニュー部分
    $html_page = rep($html_page,'menu',$menu);

    #日付部分
    $html_page = rep($html_page,'year',date("Y"));
    $html_page = rep($html_page,'month',date("n"));
    $html_page = rep($html_page,'day',date("j"));

    $param = array('onload'=>null,'deftab'=>null,'keywords'=>null,
                      'title'=>null,'description'=>null) ;

    $param = whash($param,$contents->getall());
    return phash($param,$html_page);
}

?>
