<?php

userload('view_detail/view_detail_model.php');
userload('gallery_detail/gallery_detail_model.php');
userload('group_edit/group_edit_model.php');

/**
 * ユーザーのギャラリー表示用クラス
 */
class user_gallery {

    /**
     * 表示情報の詳細
     * @var hash
     */
    public $viewinfo;

    /**
     * コンストラクタ
     * @param hash $viewinfo 表示データ
     */
    public function __construct($viewinfo) {
        $this->viewinfo = $viewinfo;
    }

    /**
     * インスタンスを作成する
     * @param int $viewid 表示位置ID
     * @return user_gallery
     */
    public static function createInstance($viewid) {
        $viewinfo = view_detail::getDetail($viewid);
        if ($viewinfo['id'] == null)
            return null;
        return new user_gallery($viewinfo);
    }

    public function getGroupDetail($id){
        if(count($id) > 1) return array('group_title'=>null,'group_comment'=>null);

        $group = new group_edit();
        $val = $group->getDetail($id[0]);
        return array('group_title'=>$val['title'],'group_comment'=>$val['comment']);
    }

    /**
     * 表示するグループ決定する
     * @return array
     */
    public function getViewGroup() {

        #使用するグループ
        $grouplist = view_detail_relation::getGroup($this->viewinfo['id']);

        $grouplist = array_keys($grouplist);


        #表示するグループを決定する
        switch ($this->viewinfo['group_view']) {
            #すべてのグループを使用する
            case VIEWDETAIL_GROUP_VIEW_TOTAL: {
                    $viewgroup = $grouplist;
                }break;

            #ランダムでひとつ取出す
            case VIEWDETAIL_GROUP_VIEW_RANDOM: {
                    if(count($grouplist)){
                        $gid = array_rand($grouplist,1);
                        $viewgroup = array($grouplist[$gid]);
                    }
                    else $viewgroup = array();
                }break;

            #キャッシュからデータを読み込んで次を表示する
            case VIEWDETAIL_GROUP_VIEW_ACCESS: {
                    $viewgroup = array($this->incrementGroup($grouplist));
                }break;
        }
        return $viewgroup;
    }

    /**
     * キャッシュ情報を読み出す
     * @return int
     */
    private function incrementGroup($grouplist) {
        $viewid = $this->viewinfo['id'];
        $cache = userpath('gallery_detail/cache/group.cache');

        if(file_exists($cache)){
            $access = (filemtime($cache) <  strtotime($this->viewinfo['editdatetime']))
                    ? array()
                    : unserialize(file_get_contents($cache));
        }
        else $access = array();

        #保存しているデータ
        $ret = $access[$viewid];

        foreach ((array)$grouplist as $id) {
            #一回目ループ
            if($first === null){
                $first = $id;
                $ret = $ret === null ? $first : $ret;
            }

            #今回の表示と同じ場合は、次のデータを取り出す
            if ($ret == $id) {$nexton = true;continue;}

            #次のデータを取り出してループ終わり
            if ($nexton) {$new = $id;break;}
        }

        #データが一個しかない場合は $new = null
        $new = $new === null ? $first : $new;

        #次に表示するデータの確定
        $access[$viewid] = $new;
        file_put_contents($cache, serialize($access));

        $ret = $ret === null ? $first : $ret;
        return $ret;
    }


    /**
     * グループの一覧から表示可能な
     * @param array $viewgroup
     */
    public function getAllGallery($viewgroup) {
        #対象となるお勧めデータを取り出す(全データ)
        #日付・表示チェック修正
        return gallery_detail::getDetailbyGroupid($viewgroup);
    }

    /**
     * 表示データを取得する
     * @param int $galleryid
     */
    public function getGallery($galleryid){
        $galleryid = intval($galleryid);
        return gallery_detail::getDetailbyViewCond($galleryid);
    }


    /**
     * ギャラリー内の最終更新日を取得する
     * @param table $allgallery
     * @return hash getdate()の値
     */
    public function getLastWriteDate($allgallery, $nothing_today = true) {
        $maxdate = 0;
        foreach ($allgallery as $row) {
            $time = strtotime($row['editdatetime']);

            $maxdate = $maxdate < $time ? $time : $maxdate;
        }
        if ($maxdate == 0 && $nothing_today){
            $maxdate = time();
        }
        return $maxdate;
    }

    /**
     * 表示内容の確認
     * @return int
     */
    public function getViewCount() {
        return $this->viewinfo['row_count'] * $this->viewinfo['column_count'];
    }

    /**
     * 表示するギャラリーのデータを選択する
     * @param table $allgallery
     * @param int $count
     */
    public function getSelectGallery($allgallery, $count,$groups) {

        #表示方法から、表示するお勧めデータを決定する
        switch ($this->viewinfo['image_view']) {
            #ランダムの場合
            case VIEWDETAIL_IMAGE_VIEW_RANDOM: {
                    shuffle($allgallery);
                    if($count >= count($allgallery)) return $allgallery;
                    $view_gallery = array_slice($allgallery, 0, $count);
                }break;

            #表示順の場合
            case VIEWDETAIL_IMAGE_VIEW_ORDER: {
                    if($count >= count($allgallery)) return $allgallery;
                    $imgtop = $this->incrementImage(count($allgallery), $count,$groups);

                    $top = array_slice($allgallery,$imgtop,$count);
                    $last = $count - count($top);
                    $next = $last > 0
                        ? array_slice($allgallery, 0, $count - count($top))
                        : array();
                    $view_gallery = array_merge($top, $next);

                }break;
             #表示順位固定の場合
             case VIEWDETAIL_IMAGE_VIEW_FIX :{
                 $view_gallery = array_slice($allgallery, 0, $count);
             }
        }
        return $view_gallery;
    }

    /**
     * データの読込み開始位置を取得する
     * @param int $totalcount
     * @param int $count
     * @return int
     */
    private function incrementImage($totalcount, $count,$groups) {
        $viewid = intval($this->viewinfo['id']);
        $imgcache = userpath('gallery_detail/cache/viewimg.cache');

        if(file_exists($imgcache)){
            $imgaccess = (filemtime($imgcache) <  strtotime($this->viewinfo['editdatetime']))
                    ? array()
                    : unserialize(file_get_contents($imgcache));
        }
        else $imgaccess = array();

        $groupid = join(',', (array)$groups);

        $imgtop = $imgaccess[$viewid][$groupid] === null ? 0 : $imgaccess[$viewid][$groupid];


        #管理画面内のデータの編集で限界値を超える場合がある。
        #初期化する
        if($totalcount <= $imgtop){ $imgtop = 0;}

        #次のデータを算出する
        $nextimg = $imgtop + $count;
        if ($nextimg >= $totalcount) {
            $nextimg = $nextimg - $totalcount;
        }

        #データを保存する
        $imgaccess[$viewid][$groupid] = $nextimg;

        file_put_contents($imgcache, serialize($imgaccess));
        return $imgtop ;
    }

    /**
     * イメージのサイズを作成する
     * @param table $view_gallery
     * @return array [index] = path
     */
    public function createFitSizeThumbnail($view_gallery) {
        $viewid = $this->viewinfo['id'];


        #表示するイメージの加工が終わっていなければ、サイズ縮小
        foreach ((array)$view_gallery as $index => $viewone) {
            $date = dirname($viewone['saveimgpath']);
            $path = SYSTEM_FOLDER . 'image/gallery/' . $date . '/mini-' . $viewid . '-' . basename($viewone['saveimgpath']);
            $old =  SYSTEM_FOLDER . 'image/gallery/' . $viewone['saveimgpath'];

            #ファイルがない場合
            if (!file_exists($path)) {
                imagesize2($old, $path, $this->viewinfo['thumbnail_width'], $this->viewinfo['thumbnail_height']);
            }
            #設定が更新された場合
            elseif(filemtime($path) < strtotime($this->viewinfo['editdatetime'])){
                imagesize2($old, $path, $this->viewinfo['thumbnail_width'], $this->viewinfo['thumbnail_height']);
            }
            #写真自体が更新されている場合
            elseif(filemtime($old) > strtotime($this->viewinfo['editdatetime'])){
                imagesize2($old, $path, $this->viewinfo['thumbnail_width'], $this->viewinfo['thumbnail_height']);
            }

            $viewone['big_path'] = $old;
            $viewone['mini_path'] = $path;

            if($this->viewinfo['event_type'] == VIEWDETAIL_EVENT_TYPE_POPUP){
                $viewone['popup_path'] = $this->createFitSizePopup($viewone);
            }
            $ret[$viewone['id']] = $viewone;
        }
        return $ret;
    }

    /**
     * ポップアップイメージようにイメージのサイズを変更する
     * @param string $viewone
     */
    public function createFitSizePopup($viewone) {
        $viewid = $this->viewinfo['id'];
        $date = dirname($viewone['saveimgpath']);
        $path = SYSTEM_FOLDER . 'image/gallery/' . $date . '/popup-' . $viewid . '-' . basename($viewone['saveimgpath']);
        $old =  SYSTEM_FOLDER .'image/gallery/' . $viewone['saveimgpath'];
        #ファイル自体がない場合
        if (!file_exists($path)) {
            imagesize2($old, $path, $this->viewinfo['img_width'], $this->viewinfo['img_height']);
        }
        #設定が更新されている場合
        elseif(filemtime($path) < strtotime($this->viewinfo['editdatetime'])){
            imagesize2($old, $path, $this->viewinfo['img_width'], $this->viewinfo['img_height']);
        }
        #写真自体が更新されている場合
        elseif(filemtime($old) > strtotime($this->viewinfo['editdatetime'])){
            imagesize2($old, $path, $this->viewinfo['img_width'], $this->viewinfo['img_height']);
        }
        return $path;
    }

}


?>
