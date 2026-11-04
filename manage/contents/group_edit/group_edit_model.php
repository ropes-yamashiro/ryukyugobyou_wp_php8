<?php

userload('gallery_detail/gallery_detail_model.php');

class group_backlist{
    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        return array('pageno'=>null,'count'=>null);

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

/**
 * グループ一覧
 * @author takezaki
 */
class group_edit {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;

    //==========================================================================
    // フィールド関連

    /**
     * 権限を確認する
     * @return array
     */
    public static function checkAuth() {
        $errval = array();
        if(!webauth(GROUP_AUTH_EDIT)) {
            $errval = array('auth'=>'編集する権限がありません');
        }
        return $errval;
    }

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #一度のアクセスでいちコネクションで済むように
        $param = '@group_edit';
        if($GLOBALS[$param] == null) {
            $db = getDB();
            $obj = $db->getNullMember('group_edit');
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
        foreach($param as $key=>$value) {
            $name = ($prefix)
                    ? $prefix . '.' . $key
                    : $key;
            $ret[$name] = webparam($key);
        }
        if(trim($ret['comment']) == '<br />' || trim($ret['comment']) == '&nbsp;') $ret['comment'] = null;
        $ret['comment'] = str_replace('\\"','"', $ret['comment']);
        return $ret;
    }

    //==========================================================================
    // データ取出し関連

    /**
     * ソートされた一覧を取得する
     * @return table
     */
    public static function getListOrdered(){
        $db = getDB();
        $sql = 'SELECT `id`,`title`,`sort` FROM `group_edit` ORDER BY `sort` DESC';
        return $db->QueryRow($sql);
    }

    /**
     * 詳細データを取り出す
     * @param int $id
     * @return hash
     */
    public static function getDetail($id) {

        #gallery_list周りができたらそのデータを取り出せるように修正
        $sql = 'SELECT * FROM `group_edit` WHERE `id`=:id';
        $db = getDB();
        $param = array('id'=>$id);
        $ret =  $db->QueryLine($sql, $param);

        if(count($ret) == 0){
            $ret = self::getNullMember();
        }
        return $ret;
    }




    //==========================================================================
    // ソート関連

    /**
     * ソートの最大番号を取得する
     */
    public static function getSortNextNo(){
        $db = getDB();
        $sql = 'SELECT max(`sort`) FROM `group_edit`';
        $row = $db->QueryLine($sql);
        return $row['max(`sort`)'] + 1;
    }

    /**
     * 現在の表示順位を取得する
     * @param int $id ソートするデータのID
     * @param bool $out SQLなどの情報を出力するか
     */
    public static function getSortNumber($id){
        $db = getDB();

        $sqlsort = 'SELECT `sort` FROM `group_edit` WHERE `id`=:id';
        $row = $db->QueryLine($sqlsort, array('id'=>$id));

        $param  = array('sort'=>$row['sort']);
        $where  = 'WHERE `sort` >= :sort ';

        $sqlnumber = 'SELECT count(*) FROM `group_edit` ' . $where;
        return $db->Count($sqlnumber, $param);
    }

    /**
     * $idのデータを$groupを基準に移動させる
     * @param int $id
     * @param int $updown
     * @param int $size
     */
    public static function rotateItem($id,$updown,$size){

        #-----------------------------------------------------------------------
        $sql_getsort = 'SELECT `sort` FROM `group_edit` WHERE `id`=:id';

        #-----------------------------------------------------------------------
        #上下切換え
        $where = $updown == self::MOVE_TYPE_DOWN
            ? ' WHERE `sort` <= :sort '
            : ' WHERE `sort` >= :sort ';
        #取出す順序
        $append = ' ORDER BY `sort` ' .
            (($updown == self::MOVE_TYPE_DOWN) ? ' DESC ' : ' ASC ');

        #データの取出し個数　データの個数
        $append .= $size > 0 ? ' LIMIT 0,' . (intval($size) + 1) : '';

        #SQL文の作成
        $sql_target = 'SELECT `id`,`sort` FROM `group_edit` ' . $where . $append;

        #-----------------------------------------------------------------------
        $db = getDB();

        $db->pdo->beginTransaction();

        #ローテートする一覧を取出す
        $row_getsort = $db->QueryLine($sql_getsort,array('id'=>$id));
        $sort = $row_getsort['sort'];
        $tbl = $db->QueryRow($sql_target,array('sort'=>$sort));

        #SQL文を作成

        $sql = $db->UpdateSql('group_edit', array('sort'), array('id'));

        #小さいものから並べる
        $lastid = null; $sortnum = null;
        foreach((array)$tbl as $row){
            if($lastid == null){
                $lastid = $row['id'];
                $sortnum = $row['sort'];
            }
            else{
                #実行
                $db->ExecuteRow($sql, array('sort'=>$sortnum,'id'=>$row['id']));

                #次のループのために
                $sortnum = $row['sort'];
            }
        }

        #数が２個以上なら
        if(count($tbl) >= 2){
            $db->ExecuteRow($sql, array('sort'=>$sortnum,'id'=>$lastid));
        }
        $rid = self::getSortNumber($id);
        $db->pdo->commit();

        #-----------------------------------------------------------------------
        return $rid;
    }


    //==========================================================================
    // 保存関連

    /**
     * エラーチェックを行う
     * @param array $input 
     */
    public static function checkValue($input){
        $err = array();
        if($input['title'] == null) $err['title']= 'グループ名を入力してください';
        if(trim($input['title']) == 'すべてのグループ') $err['title']= 'グループ名に「すべてのグループ」は使用できません';
        if(trim($input['title']) == '未分類') $err['title']= 'グループ名に「未分類」は使用できません';
        if(trim($input['title']) == '選択しない') $err['title']= 'グループ名に「選択しない」は使用できません';
        if(self::hasSameTitle($input['id'], $input['title']))
            $err['title'] = '「' . $input['title'] . '」はすでに存在しています';
        return $err;
    }

    /**
     * ID以外で、titleを持つデータがあるかどうか
     * @param int $id
     * @param string $title
     */
    public static function hasSameTitle($id,$title){
        $db = getDB();
        $sql = 'SELECT count(*) FROM `group_edit` WHERE `title`=:title and `id` != :id';
        return $db->Count($sql, array('id'=>$id,'title'=>$title)) > 0;
    }

    /**
     * データを保存する
     * @param hash $value
     * @return $id
     */
    public static function saveValue($value){
        $db = getDB();
        #上書き
        if($value['id']){
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $cond = array('id');
            $target = array('title','editdatetime','comment');
            $db->Update('group_edit', $value, $cond,$target);
        }
        #新規作成
        else{
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $value['createdatetime'] = date('Y-m-d G:i:s');
            $id = $db->Insert('group_edit', $value, array('id','sort'));

            #ソートの番号を決定する
            $db->pdo->beginTransaction();
            $value['sort'] = self::getSortNextNo();
            $value['id'] = $id;

            $cond = array('id');
            $target =  array('sort');
            $db->Update('group_edit', $value,$cond,$target);
            $db->pdo->commit();
        }
        return $value;
    }

    /**
     * グループの一覧を削除する
     * @param array $grouplist グループIDの一覧
     */
    public static function removeGroup($grouplist){
        $db = getDB();
        $db->pdo->beginTransaction();
        foreach((array)$grouplist as $gid){
            #グループに所属しているデータをすべてのグループに移す
            gallery_detail::changeGroup($gid, 0);

            #グループの削除
            $sql = 'DELETE FROM `group_edit` WHERE id =:id ';
            $db->ExecuteRow($sql, array('id'=>$gid));

            #リレーションデータも削除する
            userload('view_detail_relation/view_detail_relation_model.php');
            view_detail_relation::removeByGroupEdit($gid);
        }
        $db->pdo->commit();
    }


    /**
     * テーブルを作成する
     * @param bool $reset リセットするかどうか
     */
    public static function createTable($reset = true) {
        $db = getDB();

        #テーブル削除
        if($reset) {
            $drop = 'DROP TABLE IF EXISTS `group_edit`;';
            $db->ExecuteRow($drop);
        }

        #テーブル作成
        $create = '
        CREATE TABLE IF NOT EXISTS `group_edit` (
        `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
        `sort` INTEGER COMMENT "表示順位",
        `createdatetime` datetime COMMENT "データ作成日",
        `editdatetime` datetime COMMENT "データ編集日",
        `title` VARCHAR(255) COMMENT "タイトル",
        `comment` VARCHAR(512) COMMENT "コメント追加",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="画像のグループ設定"';
        $db->ExecuteRow($create);

        self::patchTable();
    }

    public static function patchTable(){
        $db = getDB();

        $element = $db->getNullMember('group_edit');
        if(!array_key_exists('comment',$element)){
            $sql = "ALTER TABLE `group_edit` ADD `comment` VARCHAR( 512 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'コメント追加';";
            $db->ExecuteRow($sql);
        }
    }
}
?>
