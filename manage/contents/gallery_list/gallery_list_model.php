<?php

#===============================================================================
#　
/**
 * ギャラリーの詳細
 * @author takezaki
 */
class gallery_list {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;

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

    /**
     * グループごとにすべてのグループを取り出す
     * @param int $groupid
     * @return int
     */
    public static function getCountByPaging($groupid){
        $param = array();
        $db = getDB();
        $sql = 'SELECT count(*) FROM `gallery_detail` ';
        if($groupid != '-1'){
            $sql .= 'WHERE `groupid`=:groupid';
            $param['groupid'] = $groupid;
        }
        return $db->Count($sql, $param);
    }

    /**
     * データの一覧を取り出す
     * @param int $groupid
     * @param int $pageno
     * @param int $pagecount
     * @return table
     */
    public static function getListByPaging($groupid,$pageno,$pagecount){

        $db = getDB();
        #groupid = -1(すべてのグループ)に対応するグループタイトルが、
        #group_editテーブルにはないため、LEFT JOINを使用する

        $sql = '
            SELECT
                d.`id`, d.`isvisible`
                ,d.`useviewterm`,d.`fromdate`,d.`finishdate`
                ,d.`title`,d.`saveimgpath`,d.`comment1`,g.`title` as `groupname`
                
            FROM `gallery_detail` as d
            LEFT JOIN `group_edit` as g
            ON   d.`groupid` = g.`id` ';

        $sort = ' ORDER BY d.`sort` DESC ';
        if($groupid != '-1'){
            $sql .= 'WHERE `groupid`=:groupid ' .$sort ;
            $param['groupid'] = $groupid;
        }
        else{
            $sql .= $sort;
            $param = array();
        }
        $start = ($pageno - 1) * $pagecount;
        return $db->QueryLimit($sql, $start, $pagecount, $param);
        
    }

    /**
     * 項目の表示・非表示を切り替える
     * @param array $idlist
     */
    public static function changeVisible($idlist){
        if(count($idlist) == 0)return;

        $db = getDB();
        $db->pdo->beginTransaction();
        $sql_select = 'SELECT isvisible,id FROM `gallery_detail` WHERE id=:id';
        foreach((array)$idlist as $id){
            $one = $db->QueryLine($sql_select, array('id'=>$id));
            if($one['id'] == null) continue;

            $one['isvisible'] = $one['isvisible'] == '1' ? '0' : '1';
            $db->Update('gallery_detail', $one, array('id'), array('isvisible'));
        }
        $db->pdo->commit();
    }

    /**
     * 項目を削除する
     * @param array $idlist
     */
    public static function removeGallery($idlist){
        if(count($idlist) == 0)return;
        userload('gallery_detail/gallery_detail_model.php');
        $db = getDB();
        $db->pdo->beginTransaction();
        foreach((array)$idlist as $id){
            gallery_detail::deleteValue($id);
        }
        $db->pdo->commit();
    }

    /**
     * 項目のIDを切り替える
     * @param array $idlist
     * @param int $togroup
     */
    public static function changeGroup($idlist,$togroup){
        if(count($idlist) == 0)return;
        userload('gallery_detail/gallery_detail_model.php');
        $db = getDB();
        $db->pdo->beginTransaction();
        foreach((array)$idlist as $id){
            gallery_detail::changeGroupByID($id,$togroup);
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
        userload('gallery_detail/gallery_detail_model.php');
        gallery_detail::rotateItem($id, $groupid, $updown, $size);
    }

}
?>
