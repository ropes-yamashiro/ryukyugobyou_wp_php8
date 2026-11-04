<?php
class fileUploader {

    /**
     * ファイルをアップロードする
     * @param array $data $_FILE[name]の値
     * @param string $imgfolder 保存元フォルダ
     * @return array tmppath,tmpname,filename,datetime,success
     */
    public function upload($data,$imgfolder) {
        $ret = array(
                    'tmppath'=>null,
                    'subpath'=>null,
                    'tmpname'=>null,
                    'filename'=>null,
                    'datetime'=>null,
                    'success'=>false);

        #ファイル名の確定
        if($data['name'] == null) return $ret;
        $finfo = pathinfo($data['name']);
        $ext = strtolower($finfo['extension']);

        #移動先フォルダでファイル名がぶつからないように名前を工夫
        $tmpname = md5(time() + $data['tmp_name']) . '.' . $ext;

        #SUBパスを決定する
        $subfolder = date('Y-m') ;
        $subpath = $subfolder . '/' .  $tmpname;
        $tmppath = $imgfolder . $subpath;
        @mkdir($imgfolder . $subfolder);

        if(move_uploaded_file($data['tmp_name'], $tmppath)) {
            chmod($tmppath, 0644);

            #結果情報の作成
            return array(
                    'tmppath'=>$tmppath,
                    'subpath'=>$subpath,
                    'tmpname'=>$tmpname,
                    'name'=>$data['name'],
                    'datetime'=>time(),
                    'success'=>true);
        } else {
            #結果情報の作成
            return $ret;
        }
    }


    /**
     * キャッシュにデータを書き込む
     * @param array $imginfo upload()の戻り値
     * @param array $cachepath キャッシュ用のファイル
     */
    public function appendCache($imginfo,$cachepath) {

        if($imginfo['success'] == false) return;

        #ロックファイルの決定
        $lock = $cachepath . '.lock';

        #ロック開始
        $bool = $this->startLock($lock);
        if($bool != true) die('ファイルのロックに失敗しました');

        #データの読込み
        $flist = file_exists($cachepath)
                ? unserialize(file_get_contents($cachepath))
                : array();

        #データの追加
        $flist[] = $imginfo;

        #データの書き込み
        file_put_contents($cachepath, serialize($flist));

        #ロック解除
        $this->exitLock($lock);

    }

    /**
     * キャッシュからいらないファイルデータを消去する
     * @param int $oldertime
     * @param string $cachepath
     * @param string $filename
     * @return array 削除ファイルの一覧
     */
    public function removeCache($cachepath,$tmppath = null,$oldertime = 0) {
        #ロックファイルの決定
        $lock = $cachepath . '.lock';

        #ロック開始
        $bool = $this->startLock($lock);
        if($bool != true) die('ファイルのロックに失敗しました');

        #データの読込み
        $flist = file_exists($cachepath)
                ? unserialize(file_get_contents($cachepath))
                : array();


        #いらないファイルの選定
        $retlist = array();
        $savelist = array();


        foreach($flist as $row){
            if($row['subpath'] == $tmppath){$retlist[] = $row['tmppath'];}
            elseif($oldertime != 0 && $row['datetime'] + $oldertime < time()){$retlist[] = $row['tmppath'];}
            else{
                $savelist[] = $row;
            }
        }

        #データの書き込み
        file_put_contents($cachepath, serialize($savelist));

        #ロック解除
        $this->exitLock($lock);

        return $retlist;
        
    }

    /**
     * ファイルを削除する
     */
    public function removeFiles($flist) {
        foreach((array)$flist as $file){
            @unlink($file);
        }
    }

    /**
     * ロックを解除する
     * @param string $lock
     */
    private function exitLock($lock){
        if (file_exists($lock)) {@rmdir($lock);}
    }

    /**
     * ファイルをロックする
     * @param string $lock
     * @return bool
     */
    private function startLock($lock) {
        #ロック解除確認の回数
        $retry = 20;

        #既存のロックが解除されているか確認
        #ロックの仕方　専用フォルダを作成する
        while (!@mkdir($lock, 0755)) {
            if ($retry-- <= 0) {
                #ロック失敗
                return false;
            }
            #0.25秒待つ
            usleep(250000);
        }

        #ロック成功
        return true;

    }
}
?>
