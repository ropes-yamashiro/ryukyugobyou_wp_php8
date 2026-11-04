<?php

userload('gallery_detail/gallery_detail_model.php');

/**
 * グループ一覧
 * @author takezaki
 */
class view_edit {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;

    //==========================================================================
    // フィールド関連

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@view_edit';
        if($GLOBALS[$param] == null) {
            $db = getDB();
            $obj = $db->getNullMember('view_edit');
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
        return $ret;
    }

    /**
     * 一覧を取り出す
     * @return table
     */
    public static function getList(){
        $db = getDB();
        $sql = 'SELECT `title`,`id` FROM `view_edit`';
        return $db->QueryRow($sql);
    }


    //==========================================================================
    // 保存関連

    /**
     * エラーチェックを行う
     * @param array $input 
     */
    public static function checkValue($input){
        $err = array();
        if($input['title'] == null) $err['title']= '表示名を入力してください';
        if(self::hasSameTitle($input['id'], $input['title']))
            $err['title'] ='「' . $input['title'] . '」はすでに存在しています';
        return $err;
    }

    /**
     * ID以外で、titleを持つデータがあるかどうか
     * @param int $id
     * @param string $title
     */
    public static function hasSameTitle($id,$title){
        $db = getDB();
        $sql = 'SELECT count(*) FROM `view_edit` WHERE `title`=:title and `id` != :id';
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
            $target = array('title','editdatetime');
            $db->Update('view_edit', $value, $cond,$target);
        }
        #新規作成
        else{
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $value['createdatetime'] = date('Y-m-d G:i:s');
            $id = $db->Insert('view_edit', $value, array('id'));
            $value['id'] = $id;
        }
        return $value;
    }

    /**
     * 表示位置の一覧を削除する
     * @param array $positionlist 表示位置のIDの一覧
     */
    public static function removeGroup($positionlist){
        $db = getDB();
        $db->pdo->beginTransaction();
        foreach((array)$positionlist as $pid){
            #表示位置に所属するデータを削除する
            userload('view_detail/view_detail_model.php');
            view_detail::removeDetailByPosition($pid);

            #グループの削除
            $sql = 'DELETE FROM `view_edit` WHERE id =:id ';
            $db->ExecuteRow($sql, array('id'=>$pid));
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
            $drop = 'DROP TABLE IF EXISTS `view_edit`;';
            $db->ExecuteRow($drop);
        }

        #テーブル作成
        $create = '
        CREATE TABLE IF NOT EXISTS `view_edit` (
        `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
        `createdatetime` datetime COMMENT "データ作成日",
        `editdatetime` datetime COMMENT "データ編集日",
        `title` VARCHAR(255) COMMENT "タイトル",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="画像の表示位置の設定"';
        $db->ExecuteRow($create);

    }
}
?>
