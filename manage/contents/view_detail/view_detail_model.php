<?php

#===============================================================================
#　イメージをクリックされた場合の処理
define('VIEWDETAIL_EVENT_TYPE_POPUP','1'); #ポップアップを表示する
define('VIEWDETAIL_EVENT_TYPE_LINK','2');  #リンク先に飛ばす

#　イメージの表示方法
define('VIEWDETAIL_IMAGE_VIEW_ORDER','1'); #表示順にイメージを表示する　
define('VIEWDETAIL_IMAGE_VIEW_RANDOM','2');#ランダムでイメージを表示する
define('VIEWDETAIL_IMAGE_VIEW_FIX','3');   #固定で表示する

#　グループの表示方法
define('VIEWDETAIL_GROUP_VIEW_TOTAL','1'); #表示するグループをまとめる
define('VIEWDETAIL_GROUP_VIEW_ACCESS','2');#表示するグループをアクセスことに切り換える
define('VIEWDETAIL_GROUP_VIEW_RANDOM','3');#表示するグループをランダムに表示する

#　ポップアップのサイズ
define('VIEWDETAIL_POP_TYPE_FIX','1');     #ポップアップサイズ固定
define('VIEWDETAIL_POP_TYPE_MARGIN','2');  #ポップアップのサイズをマージンで指定

userload('view_detail_relation/view_detail_relation_model.php');

/**
 * ギャラリーの詳細
 * @author takezaki
 */
class view_detail {

    const MOVE_TYPE_UP = 1;
    const MOVE_TYPE_DOWN = 0;


    /**
     * すべてのグループを選択した場合すべてのグループだけになる
     * @param array $grouplist
     * @return array
     */
    public static function filterGroup($grouplist){
        foreach((array)$grouplist as $id){
            if($id == GROUP_ID_ALLSELECT) return array(GROUP_ID_ALLSELECT);
        }
        return $grouplist;
    }

    /**
     * 入力値を修正する
     * @param hash $param
     */
    public static function checkValue($param,$state){
        $err = array();
        if($param['position'] == '0' || $param['position'] == null) $err['position'] = '「ギャラリー」を指定してください。';
        if(count($state['group_edit_id']) == 0) $err['position'] = '「表示するグループ」を選択してください。';

        if($param['group_view'] != VIEWDETAIL_GROUP_VIEW_ACCESS
                && $param['group_view'] != VIEWDETAIL_GROUP_VIEW_RANDOM
                && $param['group_view'] != VIEWDETAIL_GROUP_VIEW_TOTAL){
                $err['group_view'] = '「グループの表示方法」の項目のいずれかを選択してください。';
        }
        if($param['image_view'] != VIEWDETAIL_IMAGE_VIEW_ORDER
                && $param['image_view'] != VIEWDETAIL_IMAGE_VIEW_RANDOM
                && $param['image_view'] != VIEWDETAIL_IMAGE_VIEW_FIX){
                $err['image_view'] = '「画像の表示方法」の項目のいずれかを選択してください。';
        }


        #編集権限がある場合
        if(webauth(VIEWDETAIL_AUTH_DETAIL)){
            if(!isIntval($param['thumbnail_height']) && $param['thumbnail_height'] != null){
                $err['thumbnail_height'] = '「サムネイルの画像サイズ」の「最大縦幅」に数字を入力してください。';
            }
            if(!isIntval($param['thumbnail_width']) && $param['thumbnail_width'] != null){
                $err['thumbnail_width'] = '「サムネイルの画像サイズ」の「最大横幅」に数字を入力してください。';
            }
            if(!isIntval($param['thumbnail_height']) && !isIntval($param['thumbnail_width'])) {
                $err['thumbnail_size'] = '「サムネイルの画像サイズ」の「最大縦幅」・「最大縦幅」のいずれかを入力してください。';
            }

            if(!isIntval($param['row_count'])) $err['row_count'] = '「サムネイルの並べ方」の「行」を入力してください。';
            if(!isIntval($param['column_count'])) $err['column_count'] = '「サムネイルの並べ方」の「列」を入力してください。';


            if($param['event_type'] == null){
                $err['event_type'] = '「クリック時の処理」の項目のいずれかを選択してください。';
            }
            if($param['event_type'] == VIEWDETAIL_EVENT_TYPE_POPUP){
                
                if(!isIntval($param['img_height']) && $param['img_height'] != null){
                    $err['img_height'] = '「サムネイルの画像サイズ」の「最大縦幅」に数字を入力してください。';
                }
                if(!isIntval($param['img_width']) && $param['img_width'] != null){
                    $err['img_width'] = '「サムネイルの画像サイズ」の「最大横幅」に数字を入力してください。';
                }
                if(!isIntval($param['img_height']) && !isIntval($param['img_width'])){
                    $err['img_size'] = '「ポップアップ内のイメージ」の「最大縦幅」・「最大横幅」のいずれかを入力してください。';
                }

                if($param['pop_type'] == VIEWDETAIL_POP_TYPE_FIX){
                    if(!isIntval($param['pop_height'])) $err['pop_height'] = '「ポップアップのサイズ」の「縦」を入力してください。';
                    if(!isIntval($param['pop_width'])) $err['pop_width'] = '「ポップアップのサイズ」の「横」を入力してください。';
                }
                elseif($param['pop_type'] == VIEWDETAIL_POP_TYPE_MARGIN){
                    if(!isIntval($param['margin_top'])) $err['margin_top'] = '「ポップアップのサイズ」の「上」を入力してください。';
                    if(!isIntval($param['margin_right'])) $err['margin_right'] = '「ポップアップのサイズ」の「右」を入力してください。';
                    if(!isIntval($param['margin_bottom'])) $err['margin_bottom'] = '「ポップアップのサイズ」の「下」を入力してください。';
                    if(!isIntval($param['margin_left'])) $err['margin_left'] = '「ポップアップのサイズ」の「左」を入力してください。';
                }
                else{
                    $err['pop_type'] = '「ポップアップのサイズ」の「サイズ固定」・「画像に合わせる」のいずれかを選択してください。';
                }
            }
        }
        return $err;
    }

    /**
     * データを保存する
     * @param hash $value
     * @return $id
     */
    public static function saveValue($value,$state){
        userload('view_detail_relation/view_detail_relation_model.php');
        $db = getDB();
        $value['comment'] = str_replace('\\"','"', $value['comment']);
        $value['title'] = str_replace('\\"','"', $value['title']);

        #上書き
        if($value['id']){
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $cond = array('id');

            $db->pdo->beginTransaction();
                view_detail_relation::removeByViewDetail($value['id']);
                $old = $db->QueryLine('SELECT * FROM `view_detail` WHERE `id` =:id',array('id'=>$value['id']));
                $value = $value + $old;
                $db->Update('view_detail', $value, $cond,array(),array('createdatetime'));
                foreach((array)$state['group_edit_id'] as $gid){
                    view_detail_relation::saveItem($gid, $value['id']);
                }
            $db->pdo->commit();
        }
        #新規作成
        else{
            $value['editdatetime'] = date('Y-m-d G:i:s');
            $value['createdatetime'] = date('Y-m-d G:i:s');
            $db->pdo->beginTransaction();
                $id = $db->Insert('view_detail', $value, array('id'));
                foreach((array)$state['group_edit_id'] as $gid){
                    view_detail_relation::saveItem($gid, $id);
                }
                $db->pdo->commit();
            $value['id'] = $id;
        }
        return $value;
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
                    ? $prefix . '_' . $key
                    : $key;

            if(array_key_exists($name, $_POST)){
                $ret[$name] = webparam($name);
            }
        }
        if(trim($ret['title']) == '<br />' || trim($ret['title']) == '&nbsp;') $ret['title'] = null;
        if(trim($ret['comment']) == '<br />' || trim($ret['comment']) == '&nbsp;') $ret['comment'] = null;
        $ret['comment'] = str_replace('\\"','"', $ret['comment']);
        $ret['title'] = str_replace('\\"','"', $ret['title']);
        return $ret;
    }

    /**
     * ポジションからデータを読込みます
     * @param string $position
     */
    public static function getDetailByPosition($position){

        #gallery_list周りができたらそのデータを取り出せるように修正
        $sql = 'SELECT * FROM `view_detail` WHERE `position`=:position';
        $db = getDB();
        $param = array('position'=>$position);
        $ret =  $db->QueryLine($sql, $param);
        return (count($ret) == 0)
                ? self::getNullMember()
                : $ret;
    }

    /**
     * 削除する
     * @param string $position
     */
    public static function removeDetailByPosition($position){
        $db = getDB();

        #自分のデータを削除する
        $param = array('position'=>$position);
        $sql = 'SELECT `id` FROM `view_detail` WHERE `position`=:position';
        $line = $db->QueryLine($sql, $param);
        $sql = 'DELETE FROM `view_detail` WHERE `position`=:position';
        $db->ExecuteRow($sql, $param);

        #グループのデータを削除する
        userload('view_detail_relation/view_detail_relation_model.php');
        view_detail_relation::removeByViewDetail($line['id']);
    }


    /**
     * 詳細データを取り出す
     * @param int $id
     * @return hash
     */
    public static function getDetail($id) {

        #gallery_list周りができたらそのデータを取り出せるように修正
        $sql = 'SELECT * FROM `view_detail` WHERE `id`=:id';
        $db = getDB();
        $param = array('id'=>intval($id));
        $ret =  $db->QueryLine($sql, $param);
        return (count($ret) == 0)
                ? self::getNullMember()
                : $ret;
    }

    /**
     * フィールド値をハッシュキーにしたテーブルを取り出す
     */
    public static function getNullMember() {
        #シングルトンでいいでしょ。
        #一度のアクセスでいちコネクションで済むように
        $param = '@view_detail_member';
        if($GLOBALS[$param] == null) {
            $db = getDB();
            $obj = $db->getNullMember('view_detail');
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
            $drop = 'DROP TABLE IF EXISTS `view_detail`;';
            $db->ExecuteRow($drop);
        }

        #テーブル作成
        $create = '
    CREATE TABLE IF NOT EXISTS `view_detail` (
        `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
        `createdatetime` datetime COMMENT "データ作成日",
        `editdatetime` datetime COMMENT "データ編集日",
        `title` TEXT COMMENT "タイトル",
        `position` INTEGER COMMENT "表示位置",
        `comment` TEXT COMMENT "コメント内容",
        `group_view` TINYINT COMMENT "グループの表示方法",
        `image_view` TINYINT COMMENT "イメージの表示方法",
        `event_type` TINYINT COMMENT "イメージクリック時の処理方法",
        `img_height` INT COMMENT "イメージの最大縦幅",
        `img_width` INT COMMENT "イメージの最大横幅",
        `thumbnail_height` INT COMMENT "サムネイルの最大縦幅",
        `thumbnail_width` INT COMMENT "サムネイルの最大横幅",
        `row_count` INT COMMENT "イメージの行数",
        `column_count` INT COMMENT "イメージの列数",
        `pop_type` TINYINT COMMENT "ポップアップの表示種類",
        `pop_width` INT COMMENT "ポップアップ縦幅",
        `pop_height` INT COMMENT "ポップアップ横幅",
        `margin_top` INT COMMENT "マージン上",
        `margin_right` INT COMMENT "マージン右",
        `margin_bottom` INT COMMENT "マージン下",
        `margin_left` INT COMMENT "マージン左",
        PRIMARY KEY (`id`),
        UNIQUE KEY `position` (`position`)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="表示する位置についての設定を保存"';
        $db->ExecuteRow($create);

    }

}
?>
