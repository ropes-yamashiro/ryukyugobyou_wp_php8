<?php
class Redirect {

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    public final function __construct() {

        // ------------------------------------------------------------------
        // エージェントによりURLを切り替える
        // ------------------------------------------------------------------

        if($_SERVER['SCRIPT_NAME'] == '/index.php' || $_SERVER['SCRIPT_NAME'] == '/sp/index.php'){

            $agent = $_SERVER['HTTP_USER_AGENT'];
            if(preg_match("/(Android)/i", $agent)) {                                      	// SmartPhone
                $type = 10;
            } elseif(preg_match("/(iPhone)/i", $agent)) {                                  	// SmartPhone
                $type = 10;
            } elseif(preg_match("/(BlackBerry)/i", $agent)) {                               // SmartPhone
                $type = 10;
            } else {                                                                       	// PC
                $type = 0;
            }
            $request_url = $_SERVER["REQUEST_URI"];

            // スマートフォンからアクセス
            if ($type == 10) {
                if (mb_strpos($request_url, "/sp") === FALSE) {
                    if (!isset($_COOKIE['viewmode'])) {
                        // ファイルが存在した場合のみ、リダイレクトを実行
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sp/' . $_SERVER['SCRIPT_NAME'])) {
                            header("Location: http://" . $_SERVER["HTTP_HOST"]. "/sp" . $request_url);
                            exit;
                        }
                    }
                } elseif (isset($_COOKIE['viewmode'])) {
                    $request_url = str_replace("sp/", "", $request_url);
                    $script_name = str_replace("sp/", "", $_SERVER['SCRIPT_NAME']);
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $script_name)) {
                        header("Location: " . $request_url);
                        exit;
                    }
                }

            // PCからアクセス
            } else {

                if (mb_strpos($request_url, "/sp") === FALSE) {
                } else {
                    $request_url = str_replace("sp/", "", $request_url);
                    $script_name = str_replace("sp/", "", $_SERVER['SCRIPT_NAME']);
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $script_name)) {
                        header("Location: " . $request_url);
                        exit;
                    }
                }
            }
        }
    }
}
?>