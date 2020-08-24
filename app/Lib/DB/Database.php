<?php
namespace App\Lib\DB;

use PDO;
use Exception;

/**
 * 数据库操作类
 */
class Database{
    protected $pdo;

    public function __construct($conn){
        $this->pdo = $conn;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 格式化数据库的错误信息
     * @return string
     */
    protected function formatErrorInfoAsString(array $errorInfo, string $sql){
        return sprintf("SQLSTATE[%s]: %s (SQL: %s)", $errorInfo[0], $errorInfo[2], $sql);
    }

    /**
     * 执行非select语句：insert/update/delete/replace
     * @param string $pdo_sql; 'Update fruit set calories = :calories, colour = :colour' WHERE id = :id;
     * @param array $pdo_data; [':id' => 1, ':calories' => 150, ':colour' => 'red']
     * @return bool
     */
    public function execute(string $pdo_sql, array $pdo_data = null){
        $sth = $this->pdo->prepare($pdo_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($sth === false){
            throw new Exception($this->formatErrorInfoAsString($this->pdo->errorInfo(), $pdo_sql));
        }

        $r = $sth->execute($pdo_data);
        if($r === false){
            throw new Exception($this->formatErrorInfoAsString($sth, $pdo_sql));
        }

        return $r;
    }

    /**
     * 执行select语句
     * @param string $pdo_sql; 'SELECT name, colour, calories FROM fruit WHERE calories < :calories AND colour = :colour';
     * @param array $pdo_data; [':calories' => 150, ':colour' => 'red']
     * @param int $mode
     * @return array
     */
    public function select($pdo_sql, $pdo_data = null, $mode = PDO::FETCH_ASSOC){
        $sth = $this->pdo->prepare($pdo_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($sth === false){
            throw new Exception($this->formatErrorInfoAsString($this->pdo->errorInfo(), $pdo_sql));
        }

        $r = $sth->execute($pdo_data);
        if($r === false){
            throw new Exception($this->formatErrorInfoAsString($sth, $pdo_sql));
        }

        $r = $sth->fetchAll($mode);

        return $r;
    }

    /**
     * 执行事物
     */
    public function transaction(\Closure $func, $args = []){
        try{
            $this->pdo->beginTransaction();
            $r = $func(...$args);
            $this->pdo->commit();
        }catch(\Exception $e){
            $this->pdo->rollBack();
            throw $e;
        }

        return $r;
    }

    // /**
    //  * 插入一条数据
    //  * @example insert('user', ['id' => 1, 'name' => 'xxx']);
    //  * @param string $table
    //  * @param array $data
    //  * @return bool
    //  */
    // public function insert(string $table, array $data){
    //     $key_arr = array();
    //     $qto_arr = array();
    //     $pdo_data = [];

    //     foreach($data as $k => $v){
    //         $key_arr[] = "`$k`";
    //         $qto_arr[] = ":$k";
    //         $pdo_data[":$k"] = $v;
    //     }
    //     $key_str = implode(',', $key_arr);
    //     $prp_str = implode(',', $qto_arr);

    //     $pdo_sql = "INSERT INTO `$table` ($key_str) VALUES ($prp_str)";
    //     return $this->execute($pdo_sql, $pdo_data);
    // }

    // /**
    //  * 替换一条数据
    //  * @example replace('user', ['id' => 1, 'name' => 'xxx']);
    //  * @param string $table
    //  * @param array $data
    //  * @return bool
    //  */
    // public function replace(string $table, array $data){
    //     $key_arr = array();
    //     $qto_arr = array();
    //     $pdo_data = [];

    //     foreach($data as $k => $v){
    //         $key_arr[] = "`$k`";
    //         $qto_arr[] = ":$k";
    //         $pdo_data[":$k"] = $v;
    //     }
    //     $key_str = implode(',', $key_arr);
    //     $prp_str = implode(',', $qto_arr);

    //     $pdo_sql = "REPLACE INTO `$table` ($key_str) VALUES ($prp_str)";
        
    //     return $this->execute($pdo_sql, $pdo_data);
    // }

    // /**
    //  * 更新数据
    //  * @example update('user', ['name' => 'xxx'], ['id' => '1', 'age' => '> 1'])
    //  * @param string $table
    //  * @param array $data
    //  * @param array $condition
    //  * @return bool
    //  */
    // public function update(string $table, array $data, array $condition = null){
    //     $kv_arr = [];
    //     $pdo_data = [];

    //     foreach($data as $k => $v){
    //         $kv_arr[] = "`$k`=:$k";
    //         $pdo_data[":$k"] = $v;
    //     }

    //     if(!$condition){
    //         $pdo_sql = "UPDATE `$table` SET " . explode(",", $kv_arr);
    //     }else{
    //         # todo
    //     }
        
    //     return $this->execute($pdo_sql, $pdo_data);
    // }
    
}