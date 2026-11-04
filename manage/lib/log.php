<?php

/**
 * ログ出力ようクラス
 */
class log {
    public $filepath;
    
    /**
     * コンストラクタ
     * @param string $filepath 
     */
    public function  __construct($filepath) {
        $this->filepath = $filepath;
    }

    /**
     * ログを記述する
     * @param string $text
     */
    public function write($text,$prefix = null){
        if($prefix){
            $dir = dirname($this->filepath);
            $file = basename($this->filepath);
            $filepath = $dir . '/' . $prefix . '_'. $file;
        }
        else{
            $filepath = $this->filepath;
        }
        file_put_contents($filepath, $text . "\n", FILE_APPEND);
    }
}
?>
