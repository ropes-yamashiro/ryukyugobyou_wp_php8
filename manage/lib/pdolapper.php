<?php

/**
 * MYSQL用
 */
final class MySQLite extends PDOLite {
    /**
     * コンストラクタ
     * @param DATABASE_CONFIG $config
     */
    public function  __construct($config) {
        #mysql:dbname=db_name;host=host_name;
        $dsn = "mysql:dbname={$config->default['database']};"
                . "host={$config->default['host']};"
                . "port={$config->default['port']};";

        parent::__construct(
                $dsn,
                $config->default['login'],
                $config->default['password'],
                $config->default['encoding']
        );
    }
}

/** PDOのラッパークラス */
class PDOLite {

    const COND_KEY = 0;
    const COND_IF = 1;

    /**
     * PDOオブジェクト
     * @var PDO
     */
    public $pdo;

    /**
     * SQL文を出力するかどうか
     * @var bool
     */
    public $sqlout = false;

    /**
     * パラメーターを出力するかどうか
     * @var bool
     */
    public $paramout = false;

    /**
     * 戻り値を表示するかどうか
     * @var bool
     */
    public $retout = false;


    /**
     * コンストラクタ
     */
    public function  __construct($dsn,$user,$pass,$encode = 'utf-8') {
        try {
            $this->pdo = new PDO($dsn,$user,$pass);
            $this->pdo->exec("SET NAMES '{$encode}'");

//            $this->ExecuteRow("SET NAMES '{$encode}'");
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /** デストラクタ */
    public function __destruct() {
        unset($this->pdo);
    }

    /**
     * 値をバインドする
     * @param PDOStatement $stmt
     * @param <type> $row
     * @return <type>
     */
    private function BindRow($stmt,$row) {
        if(true) {
            $var = $stmt->queryString;
            foreach($row as $key => $val) {
                $size_t = strlen($var);
                #$var = str_replace($key, '', $var, 1);
                $var =  preg_replace("/(:{$key})([^0-9a-zA-Z]|$)/" ,'',$var);
                $size_e = strlen($var);
                if($size_e == $size_t) {
                    /*$errkey = $key;
                    echo $var . "\n";
                    $message = "<br>{$key}は必要ありません<BR>\n";
                    echo $message;*/
                }
                else {
                    $ok = $key;
                }
            }
        }

        foreach($row as $key => $value) {
            $ret[':' . $key] = $value;
            #$stmt->bindParam(':' . $key,$value);
			if (empty($value) && $value != "0") {
				$value = null;
			}
            $stmt->bindValue(':' . $key,$value);
        }
        return $ret;
    }

    /** SQL文を実行します。テーブルは取得しない
     * @param $sqlf SQL文
     * @param $row SQL内の変換用ハッシュ
     * @param $tran 単体Queryでトランザクションを使用するか？
     * @param $insert 最新のインデックス値を入れるカラム(Insert以外では使用しないこと)
     */
    public function ExecuteRow($sql,$row = array(),$insert = false) {
        try {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare($sql);
            $this->BindRow($stmt,$row);
            $res = $stmt->execute();
            if($this->sqlout) {
                var_dump('========================================');
                var_dump($sql);
            }
            if($this->paramout) {
                var_dump('----------------------------------------');
                var_dump($row);
            }
            if(!$res) {
                var_dump($sql);
                var_dump($pdo->errorCode());
                var_dump($pdo->errorInfo());
                var_dump($stmt->errorCode());
                var_dump($stmt->errorInfo());
            }
            #トランザクションをかますこと...
            if($insert) {
                $id = $pdo->lastInsertId();
            }
        }catch(Exception  $e) {
            die($e->getMessage());
        }
        return $insert ? $id : $row;
    }

    /**
     * テーブルをインデックス化する
     * @param array $table
     * @param string $key
     */
    public function getKeyTable($table,$key = 'id'){
        $ret = array();
        foreach((array)$table as $row){
            $ret[$row[$key]] = $row;
        }
        return $ret;
    }

    /**
     * テーブルのNULL情報を取り出す
     * @param string $table テーブル名
     * @return hash
     */
    public function getNullMember($table) {
        $sql = 'SHOW COLUMNS FROM `' . $table . '`';
        $tbl = $this->QueryRow($sql);
        foreach($tbl as $row) {
            $obj[$row['Field']] = null;
        }
        return $obj;
    }

    /**
     * 必要なキーだけ取出す
     * @param hash $value
     * @param list $onvalue
     * @return hash
     */
    public function onlyKey($value,$onvalue) {
        $save = array();
        foreach((array)$onvalue as $name) {
            $save[$name] = $value[$name];
        }
        return $save;
    }

    /**
     * キーからいらない値を取り出す
     * @param hash $value
     * @param list $offvalue
     * @return hash
     */
    public function removeKey($value,$offvalue) {
        $save = array();
        foreach((array)$value as $name => $val) {
            if(in_array($name, $offvalue)) continue;
            $save[$name] = $value[$name];
        }
        return $save;
    }

    /**
     * 値にフィルターをかける
     */
    public function filterKey($value,$offvalue,$onlyvalue) {
        if(count($onlyvalue)) {
            return $this->onlyKey($value, $onlyvalue);
        }
        elseif(count($offvalue)) {
            return $this->removeKey($value, $offvalue);
        }
        else {
            return $value;
        }
    }

    /**
     * 条件で使用する値を取り出す
     * @param array $value
     * @param hash $condition
     */
    public function filterCondValue($value,$condition) {
        $ret = array();
        if(is_array($condition[self::COND_KEY])) {
            foreach($condition as $row) {
                $ret[$row[self::COND_KEY]] = $value[$row[self::COND_KEY]];
            }
        }
        else {
            $ret[$condition[self::COND_KEY]] = $value[$condition[self::COND_KEY]];
        }
        return $ret;
    }


    /**
     * 削除SQL文を実行する
     * @param string $table
     * @param array $value
     * @param array $condition
     */
    public function Delete($table,$value,$condition){
        $sql = $this->DeleteSql($table, $condition);
        $this->ExecuteRow($sql, $value);
    }

    /**
     * 削除用ののSQLを作成する
     * @param string $table
     * @param mixed $condition
     * @return string
     */
    public function DeleteSql($table,$condition = array()) {
        #SQLの作成
        if(is_array($condition[self::COND_KEY])) {
            foreach($condition as $row) {
                $if = $row[self::COND_IF] ? $row[self::COND_IF] : '=';
                if($where) $where .= ' and ';
                $where = $row[self::COND_KEY] . $if . ':' . $condition[self::COND_KEY];
            }
        }
        else {
            $if = $condition[self::COND_IF] ? $condition[self::COND_IF] : '=';
            $where = $condition[self::COND_KEY] . $if . ':' . $condition[self::COND_KEY];
        }
        if($where) $where = " WHERE " . $where;
        #var_dump($table);
        #var_dump($where);
        return "DELETE FROM `{$table}` {$where}";

    }



    /**
     * UPdateを行う
     * @param テーブル名 $table
     * @param string $allvalue
     * @param mixed $condition
     * @param array $offvalue
     * @param array $onlyvalue
     */
    public function Update($table,$allvalue,$condition,$onlyvalue = array(),$offvalue =array()) {
        $upvalue = $this->filterKey($allvalue, $offvalue, $onlyvalue);
        $condvalue = $this->filterCondValue($allvalue, $condition);

        $sql = $this->UpdateSql($table, $upvalue, $condition);
        $this->ExecuteRow($sql, $upvalue + $condvalue);
    }

    /**
     * 更新用のSQLを作成する
     * @param string $table
     * @param hash $upvalue
     * @param mixed $condition
     * @return string
     */
    public function UpdateSql($table,$upvalue,$condition = array()) {
        #SQLの作成

        foreach($upvalue as $name => $val) {
            if($destfield != null) {
                $destfield .= ',';
            }
            #配列でわたってきた場合
            $target = isIntval($name) ? $val : $name;
            $destfield .= "`{$target}`=:{$target}";
        }
        if(is_array($condition[self::COND_KEY])) {
            foreach($condition as $row) {
                $if = $row[self::COND_IF] ? $row[self::COND_IF] : '=';
                if($where) $where .= ' and ';
                $where = $row[self::COND_KEY] . $if . ':' . $condition[self::COND_KEY];
            }
        }
        else {
            $if = $condition[self::COND_IF] ? $condition[self::COND_IF] : '=';
            $where = $condition[self::COND_KEY] . $if . ':' . $condition[self::COND_KEY];
        }
        if($where) $where = " WHERE " . $where;
        return "UPDATE `{$table}` SET {$destfield} {$where}";

    }

    /**
     * データを新規追加する
     */
    public function Insert($table,$value,$offvalue = array(),$onlyvalue =array()) {
        $value = $this->filterKey($value, $offvalue, $onlyvalue);
        $sql = $this->InsertSql($table, $value);
        $id = $this->ExecuteRow($sql, $value, true);
        return $id;
    }

    /**
     * データを挿入する
     * @param string $table テーブル名
     * @param hash $value 値一覧
     * @param list $offkey 保存しないキー
     * @param list $onvalue 保存するキー
     * @return ID
     */
    public function InsertSql($table,$value) {
        #SQLの作成
        foreach((array)$value as $name => $sval) {
            if($destfield != null) {
                $destfield .= ',';
                $valuefield .= ',';
            }
            $destfield .= "`{$name}`";
            $valuefield .= ":{$name}";
        }
        return "INSERT INTO `{$table}` ({$destfield}) VALUES ({$valuefield})";
    }

    /**
     * カウントを取り出す SQLは　SELECT count(*) ... のみ
     * @param  $sql
     * @param array $param
     */
    public function Count($sql,$param = array()) {
        $row = $this->QueryLine($sql, $param);
        return intval($row['count(*)']);
    }

    /**
     * 対応するデータを一つだけ取出す limit 0,1を付ける
     * @param sql $sql
     * @param array $param
     */
    public function QueryLine($sql,$param= array()) {
        $tbl = $this->QueryLimit($sql,0,1, $param);
        return $tbl[0];
    }

    /**
     * リミット情報を作る
     * @param string $sql
     * @param int $top
     * @param int $count
     * @param array $param
     * @return array
     */
    public function QueryLimit($sql,$top,$count,$param = array()) {
        return $this->QueryRow($sql . ' LIMIT ' . intval($top) . ',' . intval($count), $param);
    }

    /**
     * クエリーを実行します
     * @param $sql SQL文
     * @param $param SQL内の変換用ハッシュ
     */
    public function QueryRow($sql,$param = array()) {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare($sql);
        if($this->sqlout) {
            var_dump('========================================');
            var_dump($sql);
        }
        if($this->paramout) {
            var_dump('----------------------------------------');
            var_dump($param);
        }
        $this->BindRow($stmt,$param);
        $res = $stmt->execute();
        if(!$res) {
            var_dump($sql);
            var_dump($pdo->errorCode());
            var_dump($pdo->errorInfo());
            var_dump($stmt->errorCode());
            var_dump($stmt->errorInfo());
        }

        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($this->retout){
            var_dump('----------------------------------------');
            var_dump($ret);
        }
        return $ret;
    }
}

?>
