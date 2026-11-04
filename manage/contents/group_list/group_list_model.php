<?php

#===============================================================================
#　
/**
 * ギャラリーの詳細
 * @author takezaki
 */
class group_list {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;

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

    /**
     * グループの数を取り出す
     * @return int
     */
    public static function getCountByPaging(){
        $param = array();
        $db = getDB();
        $sql = 'SELECT count(*) FROM `group_edit` ';
        return $db->Count($sql, $param);
    }

    /**
     * データの一覧を取り出す
     * @param int $pageno
     * @param int $pagecount
     * @return table
     */
    public static function getListByPaging($pageno,$pagecount){
        $db = getDB();
        $sql = 'SELECT * FROM `group_edit` ORDER BY `sort` DESC ';
        $start = ($pageno - 1) * $pagecount;
        return $db->QueryLimit($sql, $start, $pagecount);
    }

    /**
     * 項目を削除する
     * @param array $idlist
     */
    public static function removegroup($idlist){
        if(count($idlist) == 0)return;
        group_edit::removeGroup($idlist);
    }

    /**
     * 項目のIDを切り替える
     * @param array $idlist
     * @param int $togroup
     */
    public static function changeGroup($idlist,$togroup){
        if(count($idlist) == 0)return;
        userload('group_detail/group_detail_model.php');
        $db = getDB();
        $db->pdo->beginTransaction();
        foreach((array)$idlist as $id){
            group_detail::changeGroupByID($id,$togroup);
        }
        $db->pdo->commit();
    }

    /**
     * 場所を入れ替える
     * @param int $id
     * @param int $groupid
     * @param int $updown
     * @param int $size
     */
    public static function rotate($id,$groupid,$updown,$size){
        userload('group_detail/group_detail_model.php');
        group_detail::rotateItem($id, $groupid, $updown, $size);
    }

}
?>
