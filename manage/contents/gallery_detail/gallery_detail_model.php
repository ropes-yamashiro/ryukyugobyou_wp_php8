<?php

define('GALLERY_TMP_FOLDER','image/tmp/');
define('GALLERY_FIX_FOLDER','image/gallery/');
define('GALLERY_TMP_UPPATH',sysroot() . GALLERY_TMP_FOLDER);
define('GALLERY_FIX_UPPATH',sysroot() . GALLERY_FIX_FOLDER);


define('GALLERY_TMP_CACHE' ,userpath('gallery_detail/cache/img.cache'));

class gallery_backlist{
    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        return array('group_id'=>null,'pageno'=>null,'count'=>null);

    }

    /**
     * 値をParseして取ってくる
     * @param string $prefix
     * @return hash
     */
    public static function parseValue($prefix = null) {
        $param = self::getNullMember();
        $ret = array();
        foreach($param as $key=>$value) {
            $dest = ($prefix)
                    ? $prefix . '_' . $key
                    : $key;
            $ret[$key] = webparam($dest);
        }
        return $ret;
    }
}

class group_edit_state {

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@group_edit_state';
        if($GLOBALS[$param] == null) {
            $obj = array('move_size','move_type');
            $GLOBALS[$param] = $obj;
        }
        return  $GLOBALS[$param];
    }


    /**
     * 値をParseして取ってくる
     * @param string $prefix
     * @return hash
     */
    public static function parseValue($prefix = null) {
        $param = self::getNullMember();
        $ret = array();
        foreach($param as $value) {
            $dest = ($prefix)
                    ? $prefix . '_' . $value
                    : $value;
            $ret[$value] = webparam($dest);
        }
        return $ret;
    }
}

class gallery_detail_state {

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@gallery_detail_state';
        if($GLOBALS[$param] == null) {
            $obj = array('imgpath','realpath','move_size','move_type');
            $GLOBALS[$param] = $obj;
        }
        return  $GLOBALS[$param];
    }



    /**
     * 値をParseして取ってくる
     * @param string $prefix
     * @return hash
     */
    public static function parseValue($prefix = null) {
        $param = self::getNullMember();
        $ret = array();
        foreach($param as $value) {
            $dest = ($prefix)
                    ? $prefix . '_' . $value
                    : $value;
            $ret[$value] = webparam($dest);
        }
        return $ret;
    }

}


/**
 * ギャラリーの詳細
 * @author takezaki
 */
class gallery_detail {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;

    /**
     * 権限を確認する
     * @return array
     */
    public static function checkAuth() {
        $errval = array();
        if(!webauth(GALLERY_AUTH_EDIT)) {
            $errval = array('auth'=>'編集する権限がありません');
        }
        return $errval;
    }

    /**
     * 入力値を修正する
     * @param hash $param
     */
    public static function checkValue($param,$state) {
        $err = array();


        if($param['useviewterm'] == 1){
            if($param['fromdate'] == null) {
                $err['fromdate'] = '開始日を入力して下さい';
            }
            if($param['finishdate'] == null) {
                $err['finishdate'] = '終了日を入力して下さい';
            }
            if($param['fromdate'] > $param['finishdate']) {
                $err['dateterm'] = '開始日と終了日の前後関係を修正してください。';
            }
        }

        $param['fromdate'] = str_replace('-', '/', $param['fromdate']);
        if($param['fromdate']) {
            if(!preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $param['fromdate'])) {
                $err['fromdate'] = '開始日を 0000/00/00 の形式で記述して下さい';
            }else if(!isDate($param['fromdate'])){
                $err['fromdate'] = '開始日に 実際に存在する日付を入力して下さい';
            }
        }
        $param['finishdate'] = str_replace('-', '/', $param['finishdate']);
        if($param['finishdate']) {
            if(!preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $param['finishdate'])) {
                $err['finishdate'] = '終了日を 0000/00/00 の形式で記述して下さい';
            }else if(!isDate($param['finishdate'])){
                $err['finishdate'] = '開始日に 実際に存在する日付を入力して下さい';
            }
        }
        if($param['title'] == null) {
            $err['title'] = 'タイトルを入力して下さい';
        }

        #ファイルのアップロード
        $fname = $_FILES['imgname']['name'];
        if($fname){
            $ext = extname($fname);
            if(!in_array($ext,array('png','jpg','bmp','jpeg','gif'))){
                $err['saveimgpath'] = '「' .$ext .'」形式のファイルはアップロードできません';
            }
        }else{
            if($state['imgpath'] == null && $state['realpath'] == null){
                $err['saveimgpath'] = 'ファイルを選択してください';
            }
        }
        return $err;
    }


    /**
     * 対象のパスを削除する
     * @param string $filepath $state[imgpath]
     */
    public static function removeImage($filepath,$temp = true) {
        #途中に文字列(../.)が入ってもいいように
        $rpath = realpath(sysroot() . $filepath);
        $folder = dirname($rpath);

        $destfolder = $temp ? GALLERY_TMP_FOLDER : GALLERY_FIX_FOLDER;
        $destfolder = str_replace('/', '\\/', $destfolder);

        $sysroot = str_replace('/', '\\', sysroot());
        $folder = str_replace($sysroot, '', $rpath);
        $folder = str_replace('\\', '/', $folder);

        

        if(file_exists($rpath)
                && preg_match("/^{$destfolder}[0-9]{4}-[0-9]{2}\\//" , $folder)) {
            @unlink($rpath);
        }
    }

    /**
     * イメージをテンプとして保存する
     * @param array $data $_FILE[name]の値
     * @return array
     */
    public static function upImageAsTmp($data) {

        #アップロード
        $upload = new fileUploader();
        $imginfo = $upload->upload($data,GALLERY_TMP_UPPATH);
        $imginfo['subpath'] = GALLERY_TMP_FOLDER . $imginfo['subpath'];

        #キャッシュに保存
        if($imginfo['success']) {
            $upload->appendCache($imginfo, GALLERY_TMP_CACHE);
            return $imginfo;
        }
    }

    /**
     * イメージの完了処理を行う(デバッグ最後)
     * @param string $tmpcache
     * @return array
     */
    public static function fixImage($tmpcache) {
        $upload = new fileUploader();

        #保存先のフォルダを作成する
        $moveto = date('Y-m/');
        if(!file_exists(GALLERY_FIX_UPPATH . $moveto))
            mkdir(GALLERY_FIX_UPPATH . $moveto);

        $filename = basename($tmpcache);
        #ファイルをコピーする
        copy($tmpcache,GALLERY_FIX_UPPATH . $moveto . $filename);

        #必要のないキャッシュを削除する
        $flist = $upload->removeCache(GALLERY_TMP_CACHE, $tmpcache);

        #必要のないイメージを削除する
        $upload->removeFiles($flist, 60 * 30);

        #情報を書き換える
        return $moveto . $filename;
    }

    /**
     * 現在の表示順位を取得する
     * @param int $id ソートするデータのID
     * @param int $groupid グループID
     */
    public static function getSortNumber($id,$groupid = 0) {
        $db = getDB();

        $sqlsort = 'SELECT `sort` FROM `gallery_detail` WHERE `id`=:id';
        $row = $db->QueryLine($sqlsort, array('id'=>$id));

        if($groupid) {
            $param = array('sort'=>$row['sort'],'groupid'=>$groupid);
            $where = 'WHERE `sort` >= :sort AND `groupid` =:groupid ';
        }
        else {
            $param  = array('sort'=>$row['sort']);
            $where  = 'WHERE `sort` >= :sort ';
        }
        $sqlnumber = 'SELECT count(*) FROM `gallery_detail` ' . $where;
        return $db->Count($sqlnumber, $param);
    }

    /**
     * ソートの最大番号を取得する
     */
    public static function getSortNextNo() {
        $db = getDB();
        $sql = 'SELECT max(`sort`) FROM `gallery_detail`';
        $row = $db->QueryLine($sql);
        return $row['max(`sort`)'] + 1;
    }

    /**
     * ソートの最小番号を取得する
     */
    public static function getSortStartNo() {
        $db = getDB();
        $sql = 'SELECT min(`sort`) FROM `gallery_detail`';
        $row = $db->QueryLine($sql);
        return $row['max(`sort`)'];
    }

    /**
     * $idのデータを$groupを基準に移動させる
     * @param int $id
     * @param int $groupid
     * @param int $size
     */
    public static function rotateItem($id,$groupid,$updown,$size) {

        #-----------------------------------------------------------------------
        $sql_getsort = 'SELECT `sort` FROM `gallery_detail` WHERE `id`=:id';

        #-----------------------------------------------------------------------
        #上下切換え
        $where = $updown == self::MOVE_TYPE_DOWN
                ? ' WHERE `sort` <= :sort '
                : ' WHERE `sort` >= :sort ';
        #グループ指定
        if(intval($groupid) != -1) {
            $where .= ' and `groupid` =:groupid ';
            $param['groupid'] = $groupid;
        }
        #取出す順序
        $append = ' ORDER BY `sort` ' .
                (($updown == self::MOVE_TYPE_DOWN) ? ' DESC ' : ' ASC ');

        #データの取出し個数　データの個数
        $append .= $size > 0 ? ' LIMIT 0,' . (intval($size) + 1) : '';

        #SQL文の作成
        $sql_target = 'SELECT `id`,`sort` FROM `gallery_detail` ' . $where . $append;

        #-----------------------------------------------------------------------
        $db = getDB();

        $db->pdo->beginTransaction();

        #ローテートする一覧を取出す
        $row_getsort = $db->QueryLine($sql_getsort,array('id'=>$id));
        $sort = $row_getsort['sort'];


        $param['sort'] = $sort;
        $tbl = $db->QueryRow($sql_target,$param);
   
        #SQL文を作成
        $sql = $db->UpdateSql('gallery_detail', array('sort'), array('id'));

        #小さいものから並べる
        $lastid = null;
        $sortnum = null;
        foreach((array)$tbl as $row) {
            if($lastid == null) {
                $lastid = $row['id'];
                $sortnum = $row['sort'];
            }
            else {
                #実行
                $db->ExecuteRow($sql, array('sort'=>$sortnum,'id'=>$row['id']));

                #次のループのために
                $sortnum = $row['sort'];
            }
        }

        #数が２個以上なら
        if(count($tbl) >= 2) {
            $db->ExecuteRow($sql, array('sort'=>$sortnum,'id'=>$lastid));
        }
        $rid = self::getSortNumber($id, $groupid);
        $db->pdo->commit();

        #-----------------------------------------------------------------------
        return $rid;
    }

    /**
     * イメージを保存する
     * @param int $id
     * @param string $path
     */
    public static function saveImage($id,$path){
        $db = getDB();
        if($id) {
            $value['id'] = $id;
            $value['saveimgpath'] = $path;
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $cond = array('id');
            $tarOnly = array('editdatetime','saveimgpath');
            $db->Update('gallery_detail', $value, $cond,$tarOnly);
        }
    }

    /**
     * データを保存する
     * @param hash $value
     * @return $id
     */
    public static function saveValue($value) {
        $db = getDB();
        #上書き
        if($value['id']) {
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $cond = array('id');
            $tarOnly = array();
            $tarOff = array('saveimgpath','sort','createdatetime');
            $db->Update('gallery_detail', $value, $cond,$tarOnly,$tarOff);
        }
        #新規作成
        else {
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $value['createdatetime'] = date('Y-m-d G:i:s');
            $id = $db->Insert('gallery_detail'
                    , $value, array('id','sort'));

            #ソートの番号を決定する
            $db->pdo->beginTransaction();
            $value['sort'] = self::getSortNextNo();
            $value['id'] = $id;

            $cond = array('id');
            $target =  array('sort');
            $db->Update('gallery_detail', $value,$cond,$target);
            $db->pdo->commit();
        }
        return $value;
    }


    /**
     * 対象のデータを削除する
     * @param int $id
     */
    public static function deleteValue($id){
        $db = getDB();

        
        $db->Delete('gallery_detail', array('id'=>$id),array('id'));
    }

    public static function changeGroupByID($id,$togroup){
        $db = getDB();
        $sql = 'UPDATE `gallery_detail` SET groupid=:togroup WHERE `id` =:id ';
        $db->ExecuteRow($sql, array('togroup'=>$togroup,'id'=>$id));
    }


    /**
     * グループIDを持つアイテムを変更する
     * @param int $fromgroup 変更前の値
     * @param int $togroup 変更先の値
     */
    public static function changeGroup($fromgroup,$togroup) {
        $db = getDB();
        $sql = 'UPDATE `gallery_detail` SET `groupid`=:togroup WHERE `groupid` =:fromgroup ';
        $db->ExecuteRow($sql, array('togroup'=>$togroup,'fromgroup'=>$fromgroup));
    }


    /**
     * 値をParseして取ってくる
     * @param string $prefix
     * @return hash
     */
    public static function parseValue($prefix = null) {
        $param = self::getNullMember();
        $ret = array();
        foreach($param as $key=>$value) {
            $name = ($prefix)
                    ? $prefix . '.' . $key
                    : $key;
            $ret[$name] = webparam($key);
        }

        #echo "少しの間だけでます:{$ret['title']}:{$ret['comment1']}:{$ret['comment2']}";
        if(trim($ret['title']) == '<br />' || trim($ret['title']) == '&nbsp;') $ret['title'] = null;
        if(trim($ret['comment1']) == '<br />' || trim($ret['comment1']) == '&nbsp;') $ret['comment1'] = null;
        if(trim($ret['comment2']) == '<br />' || trim($ret['comment2']) == '&nbsp;') $ret['comment2'] = null;
        $ret['title'] = str_replace('\\"','"', $ret['title']);
        $ret['comment1'] = str_replace('\\"','"', $ret['comment1']);
        $ret['comment2'] = str_replace('\\"','"', $ret['comment2']);

        return $ret;
    }

        /**
     * グループIDの一覧から・表示チェック・有効期間チェックあり
     * @param array $groupidlist
     * @return table
     */
    public static function getDetailbyViewCond($gallery_id) {

        #gallery_list周りができたらそのデータを取り出せるように修正
        #表示チェック・有効期間チェックあり
        $sql = '
            SELECT * FROM `gallery_detail`
            WHERE
                #対象のデータ
                `id` =:id

                #表示するチェックボックスがオンになっている
                and `isvisible` = 1

                #日付設定の条件
                and
                (
                    #日付有効範囲が設定している場合
                    (`useviewterm` = 1
                    and `fromdate` <= :today
                    and `finishdate` >= :today)

                    #日付有効範囲が設定していない場合
                    or (`useviewterm` IS NULL or `useviewterm` = 0)
                )';
        $db = getDB();
        $ret =  $db->QueryLine($sql
                ,array('today'=>date('Y-m-d'),'id'=>$gallery_id));
        return $ret;
    }


    /**
     * グループIDの一覧から・表示チェック・有効期間チェックあり
     * @param array $groupidlist
     * @return table
     */
    public static function getDetailbyGroupid($groupidlist) {

        #gallery_list周りができたらそのデータを取り出せるように修正
        #表示チェック・有効期間チェックあり
        $sql = '
            SELECT * FROM `gallery_detail`
            WHERE
                #表示するチェックボックスがオンになっている
                `isvisible` = 1

                #日付設定の条件
                and 
                (
                    #日付有効範囲が設定している場合
                    (`useviewterm` = 1
                    and `fromdate` <= :today
                    and `finishdate` >= :today)

                    #日付有効範囲が設定していない場合
                    or (`useviewterm` IS NULL or `useviewterm` = 0)
                )';
        $db = getDB();

        $wherevis = 'isvisible = 1 ';
        foreach((array)$groupidlist as $id){
            $id = intval($id);
            $where .= $where ? ' or ' : '';
            $where .= "`groupid`=$id";
        }
        $where = $where ? ' and  (' . $where . ')' : '';
        $sql = $sql . $where . ' ORDER BY `sort` DESC';

        $ret =  $db->QueryRow($sql,array('today'=>date('Y-m-d')));
        return $ret;
    }

    /**
     * 詳細データを取り出す
     * @param int $id
     * @return hash
     */
    public static function getDetail($id) {

        #gallery_list周りができたらそのデータを取り出せるように修正
        $sql = 'SELECT * FROM `gallery_detail` WHERE `id`=:id';
        $db = getDB();
        $param = array('id'=>$id);
        $ret =  $db->QueryLine($sql, $param);

        if(count($ret) == 0){
            $ret = self::getNullMember();
        }
        else{
            $ret['fromdate'] = ($ret['fromdate'] == '0000-00-00')  ? null : str_replace('-', '/', $ret['fromdate']);
            $ret['finishdate'] = ($ret['finishdate'] == '0000-00-00') ? null : str_replace('-', '/', $ret['finishdate']);
        }
        return $ret;
    }

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@gallery_detail_member';
        if($GLOBALS[$param] == null) {
            $db = getDB();
            $obj = $db->getNullMember('gallery_detail');
            $GLOBALS[$param] = $obj;
        }
        return  $GLOBALS[$param];
    }

    /**
     * テーブルを作成する
     * @param bool $reset リセットするかどうか
     */
    public static function createTable($reset = false) {
        $db = getDB();
        #テーブル削除
        if($reset) {
            $drop = 'DROP TABLE IF EXISTS `gallery_detail`;';
            $db->ExecuteRow($drop);
        }

        #テーブル作成
        $create = '
        CREATE TABLE IF NOT EXISTS `gallery_detail` (
        `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
        `isvisible` TINYINT COMMENT "表示するかどうか",
        `useviewterm` TINYINT COMMENT "表示期間を使用するか",
        `fromdate` date COMMENT "表示開始日",
        `finishdate` date COMMENT "表示完了日",
        `groupid` INTEGER COMMENT "表示グループのID",
        `sort` INTEGER COMMENT "グループ内での表示番号",
        `createdatetime` datetime COMMENT "データ作成日",
        `editdatetime` datetime COMMENT "データ編集日",
        `title` TEXT COMMENT "タイトル",
        `saveimgpath` VARCHAR(50) COMMENT "イメージの保存パス(相対)",
        `linkurl` VARCHAR(512) COMMENT "リンクに使用するURL",
        `comment1` TEXT COMMENT "コメント内容1",
        `comment2` TEXT COMMENT "コメント内容2",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="表示する画像の詳細を保存"';
        $db->ExecuteRow($create);

    }

}
?>
