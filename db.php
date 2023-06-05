<?php
class DB
{
    /*
    1.自動連線資料庫
    2.能夠執行CRUD操作
    3.能指定資料表
    */
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=vote";
    protected $user = "root";
    protected $pw = "";
    protected $table;
    protected $pdo;
    protected $query_result;
    // 建構式
    function __construct($table)
    {
        $this->table = $table;
        $this->pdo = new PDO($this->dsn, $this->user, $this->pw);
    }
    function all(...$arg)
    {
        $sql = "select * from $this->table";
        if (!empty($arg)) {
            if (is_array($arg[0])) {
                foreach ($arg[0] as $key => $value) {
                    $tmp[] = "`$key`='$value'";
                }
                $sql = $sql . " where " . join(" && ", $tmp);
            } else {
                $sql = $sql . $arg[0];
            }
        }
        if (isset($arg[1])) {
            $sql = $sql . $arg[1];
        }

        $this->query_result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $this;
    }
    function find($arg)
    {
        $sql = "select * from `$this->table` where ";
        if (is_array($arg)) {
            foreach ($arg as $key => $value) {
                $tmp[] = "`$key`='$value'";
            }
            $sql .= join(" && ", $tmp);
        } else {
            $sql .= " `id` = '$arg' ";
        }
        // echo $sql;
        $this->query_result = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $this;
    }

    function save($cols)
    {
        if (isset($cols['id'])) {
            // update
            foreach ($cols as $key => $value) {
                if ($key != 'id') {
                    $tmp[] = "`$key`='$value'";
                }
            }
            $sql = "update `$this->table` set  " . join(',', $tmp) . " where `id`='{$cols['id']}'";
            // echo $sql;
            $this->query_result = $this->pdo->exec($sql);
            return $this;
        } else {
            // insert
            $key = array_keys($cols);
            $sql = "insert into $this->table (`" . join("`,`", $key) . "`)values('" . join("','", $cols) . "')";
            // echo $sql;
            $this->query_result = $this->pdo->exec($sql);
            return $this;
        }
    }
    function del($arg)
    {
        $sql = "delete from `$this->table` where ";
        if (is_array($arg)) {
            foreach ($arg as $key => $value) {
                $tmp[] = "`$key`='$value'";
            }
            $sql .= join(" && ", $tmp);
        } else {
            $sql .= " `id` = '$arg' ";
        }
        // echo $sql;
        $this->query_result = $this->pdo->exec($sql);
        return $this;
    }
    function count(...$arg)
    {
        $sql = "select count(*) from $this->table ";
        if (!empty($arg)) {
            if (is_array($arg[0])) {
                foreach ($arg[0] as $key => $value) {
                    $tmp[] = "`$key`='$value'";
                }
                $sql = $sql . " where " . join(" && ", $tmp);
            } else {
                $sql = $sql . $arg[0];
            }
        }
        if (isset($arg[1])) {
            $sql = $sql . $arg[1];
        }
        $this->query_result = $this->pdo->query($sql)->fetchColumn();
        return $this;
    }
    // ...arg不定參數
    function sum($cols, ...$arg)
    {
        return $this->math('sum', $cols, ...$arg);
    }
    function min($cols, ...$arg)
    {
        return $this->math('min', $cols, ...$arg);
    }
    function max($cols, ...$arg)
    {
        return $this->math('max', $cols, ...$arg);
    }
    function avg($cols, ...$arg)
    {
        return $this->math('avg', $cols, ...$arg);
    }

    private function math($math, $col, ...$arg)
    {
        $sql = "select $math(`$col`) from $this->table ";

        if (!empty($arg)) {
            if (is_array($arg[0])) {
                foreach ($arg[0] as $key => $value) {
                    $tmp[] = "`$key`='$value'";
                }
                $sql = $sql . " where " . join(" && ", $tmp);
            } else {
                $sql = $sql . $arg[0];
            }
        }
        if (isset($arg[1])) {
            $sql = $sql . $arg[1];
        }
        // echo $sql;
        $this->query_result = $this->pdo->query($sql)->fetchColumn();
        return $this;
    }

    function dd()
    {
        echo "<pre>";
        print_r($this->query_result);
        echo "</pre>";
    }
}
// 全能的
function q($sql){
    $dsn = "mysql:host=localhost;charset=utf8;dbname=vote";
    $pdo=new PDO($dsn,'root','');
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

$Topic = new DB('topics');
$Option = new DB('options');

// dd($Topic->count(['type'=>2]));
// dd($Topic->sum('id'));
$Topic->sum('id')->dd();
$Option->min('id')->dd();
$Option->max('total')->dd();
$Option->avg('total')->dd();

// SELECT sum(A.`id`) FROM (select * from `topics` LIMIT 2)A   <=Sql子查詢寫法

// $Topic->all(['id' => 7])->dd();
// dd($Option->del(20));
// dd($Topic->query_result);

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
