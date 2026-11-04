<?php
define('GALLERY_TMP_UPPATH',sysroot() . 'image/tmp/');
define('GALLERY_TMP_CACHE' ,userpath('view_detail_relation/img.cache'));
define('GALLERY_FIX_UPPATH',sysroot() . 'image/gallery/');

define('GROUP_ID_ALLSELECT','-1');
define('GROUP_ID_NONSELECT', '0');

/**
 * ギャラリーの詳細
 * @author takezaki
 */
class view_detail_relation {


    //==========================================================================
    // データ取出し関連


    public static function getGroup($view_detail_id){
        #使用できるデータの一覧を取り出す
        userload('group_edit/group_edit_model.php');
        $db = getDB();

        #表示グループ一覧
        $sql = '
            SELECT `group_edit_id` as `id`
            FROM `view_detail_relation`
            WHERE `view_detail_id` =:vdid';

        $table = $db->getKeyTable($db->QueryRow($sql,array('vdid'=>$view_detail_id)));

        if(count($table)){
            if($table[GROUP_ID_ALLSELECT]['id'] == GROUP_ID_ALLSELECT){
                $sql = 'SELECT id  FROM `group_edit`';
                $table = $db->QueryRow($sql);
                $table = $db->getKeyTable($table);
                $table[GROUP_ID_NONSELECT] = GROUP_ID_NONSELECT;
            }
        }

        $order = $db->getKeyTable(group_edit::getListOrdered());

        $order[GROUP_ID_NONSELECT] = array('id'=>GROUP_ID_NONSELECT,'title'=>'未分類');

        foreach($order as  $id => $order_row){
            if($table[$id] !== null) $ret[$id] = $table[$id];
        }
        return (array)$ret;
    }

    /**
     * 選択状況を含めデータを取り出す
     */
    public static function getInfo($view_detail_id){

        #使用できるデータの一覧を取り出す
        userload('group_edit/group_edit_model.php');
        $db = getDB();

        #表示グループ一覧
        $table = $db->getKeyTable(group_edit::getListOrdered());

        $db = getDB();
        $sql = '
            SELECT `group_edit_id`,`use_group`
            FROM `view_detail_relation`
            WHERE `view_detail_id` =:vdid';

        #チェックしたデータをとりだす
        $info = $db->getKeyTable(
                $db->QueryRow($sql, array('vdid'=>$view_detail_id))
                ,'group_edit_id');

        $ret = array();

        $ret[GROUP_ID_ALLSELECT] = $info[GROUP_ID_ALLSELECT]['use_group'] == '1'
            ? array('use'=>true,'title'=>'すべてのグループ','id'=>GROUP_ID_ALLSELECT)
            : array('use'=>false,'title'=>'すべてのグループ','id'=>GROUP_ID_ALLSELECT);
        
        foreach($table as $key => $row){
            $row['use'] = ($info[$key]['use_group'] == '1');
            $ret[$key] = $row;
        }
        foreach($info as $key => $row){
            if($ret[$key]) continue;
            $ret[$key] = array('use'=>true);
        }

        $ret[GROUP_ID_NONSELECT] = $info[GROUP_ID_NONSELECT]['use_group'] == '1'
            ? array('use'=>true,'title'=>'未分類','id'=>GROUP_ID_NONSELECT)
            : array('use'=>false,'title'=>'未分類','id'=>GROUP_ID_NONSELECT);

        return $ret;
    }

    /**
     * 対象となるデータを削除する
     * @param string $view_detail_id
     */
    public static function removeByGroupEdit($groupid){
        $db = getDB();
        $sql = 'DELETE FROM `view_detail_relation` WHERE `group_edit_id`=:id';
        $db->ExecuteRow($sql, array('id'=>$groupid));
    }


    /**
     * 対象となるデータを削除する
     * @param string $view_detail_id
     */
    public static function removeByViewDetail($view_detail_id){
        $db = getDB();
        $sql = 'DELETE FROM `view_detail_relation` WHERE `view_detail_id`=:id';
        $db->ExecuteRow($sql, array('id'=>$view_detail_id));
    }

    /**
     * データを追加する
     * @param string $group_id
     * @param string $view_detail_id
     */
    public static function saveItem($group_id,$view_detail_id){
        $value['editdatetime'] = date('Y-m-d G:i:s');
        $value['createdatetime'] = date('Y-m-d G:i:s');
        $value['use_group'] = 1;
        $value['view_detail_id'] = $view_detail_id;
        $value['group_edit_id'] = $group_id;
        $db = getDB();
        $id = $db->Insert('view_detail_relation', $value);
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
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@view_detail_relation_member';
        if($GLOBALS[$param] == null) {
            $db = getDB();
            $obj = $db->getNullMember('view_detail_relation');
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
            $drop = 'DROP TABLE IF EXISTS `view_detail_relation`;';
            $db->ExecuteRow($drop);
        }

        #テーブル作成
        $create = '
    CREATE TABLE IF NOT EXISTS `view_detail_relation` (
        `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
        `view_detail_id` INTEGER NOT NULL COMMENT "表示詳細のID",
        `group_edit_id` INTEGER NOT NULL COMMENT "グループのID",
        `use_group` TINYINT COMMENT "使用するかしないか",
        `createdatetime` datetime COMMENT "データ作成日",
        `editdatetime` datetime COMMENT "データ編集日",
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="表示する位置についての設定を保存"';
        $db->ExecuteRow($create);

    }

}
?>
