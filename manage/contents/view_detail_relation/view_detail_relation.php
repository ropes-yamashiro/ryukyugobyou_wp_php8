<?php

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function view_detail_relation_tbl_setup($force = false){
    #必要なクラスを読込む
    userload('view_detail_relation/view_detail_relation_model.php');
    view_detail_relation::createTable($force);
}

?>