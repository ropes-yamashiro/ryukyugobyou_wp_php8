<?php

/**
 * 元になるファイルが更新されれば読込み時コンパイルする
 * @param string $input
 * @param string $cache
 * @param array $block
 */
function cache_TempLoader($input,$cache,$block){
    if(!file_exists($cache)){
        $loader = new TempLoader($input);
        $value = $loader->cookPage($block);
        file_put_contents($cache, serialize($value));
    }
    else{
        #if(true){
        if(filemtime($cache) < filemtime($input)){
            $loader = new TempLoader($input);
            $value = $loader->cookPage($block);
            file_put_contents($cache, serialize($value));
        }
        else{
            $value = unserialize(file_get_contents($cache));
        }
    }
    return $value;

}

class TempResult{
    public $innerText;
    public $outterText;
    public $name;

    public function  __construct($out,$in,$na) {
        $this->innerText = $in;
        $this->outterText = $out;
        $this->name = $na;
    }
}

class TempLoader{

    public $encoding = 'utf-8';
    public $page = null;

    public function  __construct($filepath) {
        $this->page = file_get_contents($filepath);
    }

    /**
     * ページを加工する
     * @param array $block
     * @return hash 'page'全体部分
     */
    public function cookPage($block,$text = null){
        $ret = array();

        #データを特定する
        $page = $text ? $text : $this->page;

        #ブロックの確保
        foreach ((array)$block as $name){
            #リスト TempResult
            $dest = $this->getRegion($name,$page);
            $all = array_merge((array)$all, $dest);
        }
        $all_text = array(new TempResult($page, $page, 'page'));
        $all = array_merge((array)$all, $all_text);

        #ブロックの処理
        foreach((array)$all as $one){
            /** @var TempResult $one */
            $ret[$one->name] = $this->cookBlock($one->innerText, $all);
        }

        return $ret;
    }

    /**
     * ブロックを処理する
     * @param string $page
     * @param array $all
     * @return string
     */
    private function cookBlock($page,$all){

        #ブロックデータを削除する
        foreach((array)$all as $one){
            /** @var TempResult $one */
            $page = str_replace($one->outterText, '', $page);

            #それぞれのブロックを消す
        }

        #無視ブロックを削除
        $page = $this->removeIngore($page);
        $page = $this->removeMemo($page);

        #変数を置き換える
        $page = $this->repVar($page);

        #置き換え関数を実行する
        $page = $this->repFunction($page);

        return $page;
    }

    /**
     * rep関数部分を切り替える
     * @param string $page
     * @return string
     */
    private function repFunction($page){
        $pattern = '(<!-- rep\\("([^,]+)","([^,]*)"\\) -->)';
        preg_match_all($pattern, $page,$all, PREG_SET_ORDER);

        foreach($all as $one){
            $page = str_replace($one[0], null, $page);
            $page = str_replace($one[1], $one[2], $page);
        }
        return $page;
    }

    /**
     * ページ内のコメントを削除する
     * @param string $page
     * @return string
     */
    private function removeMemo($page){

        # [^-]*
        # -[^-]*
        # --[^>]

        $pattern = "/<!-- #MEMO#(([^-]*)|(-[^-])|(--[^>]))*-->/" ;
        $replacement = '';
        $page =  preg_replace($pattern, $replacement, $page);
        return $page;
    }


    /**
     * 変数を置き換え用に書き換える
     * @param string $page
     * @return string
     */
    private function repVar($page){
        $pattern = '/<!-- {([^}]+)} -->/i';
        $replacement = '{$1}';
        $page =  preg_replace($pattern, $replacement, $page);
        return $page;
    }

    /**
     * ページがらIngore部分を削除する
     */
    private function removeIngore($page){
        $all = $this->getRegion('INGORE', $page);
        foreach((array)$all as $one){
            /** @var TempResult $one */
            $page = str_replace($one->outterText, '', $page);
        }
        return $page;
    }

    /**
     * ブロックを取出す
     * @param string $ident
     * @param string $page
     */
    private function getRegion($ident,$page){
        $start = "<!-- #{$ident} start -->" ;
        $end = "<!-- #{$ident} end -->";


        $ret = array();
        $endsize =mb_strlen($end);
        $offset = 0;
        $size = mb_strlen($page, $this->encoding);
        while($offset < $size){

            $spos = mb_strpos($page,$start,$offset,$this->encoding);
            if($spos === false) return $ret;

            $epos = mb_strpos($page,$end,$spos,$this->encoding);
            if($epos === false) return $ret;

            $outter = mb_substr($page, $spos, $epos - $spos + $endsize,$this->encoding);
            $inner = str_replace(array($start,$end), array('',''), $outter);

            $one = new TempResult($outter, $inner,$ident);
            $ret[] = $one;

            $offset = $epos + $endsize;
        }
        return $ret;
    }
}

?>
