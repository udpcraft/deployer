<?php

namespace Components;


class Model extends \Afanty\Helper\Singleton
{
    protected $_db = null;

    protected $_table = '';

    public function __construct()
    {
        $this->_db = \Afanty\Helper\Factory::getSqlite();
    }

    public function fetchRow($sql, $bind = [])
    {
        return $this->fetchList($sql, $bind, true);
    }

    public function fetchList($sql, $bind = [], $isrow = false)
    {
        $stmt = $this->execute($sql, $bind);

        if($isrow) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert(Array $info)
    {
        $columns = array_keys($info);

        $sql = "insert into %s (%s) values (%s)";

        $sql = sprintf($sql, $this->_table, implode(',', $columns), ":" . implode(', :', $columns));

        $this->execute($sql, $info);

        return $this->_db->lastInsertId();
    }

    public function update(Array $update, Array $where)
    {
        $updateColumns = array_keys($update);
        $whereColums = array_keys($where);

        $sql = "update %s set %s where %s";

        $updateStr = '';
        $whereStr = '';

        foreach ($updateColumns as $column) {
            $updateStr .= "$column = :$column, ";
        }

        foreach ($whereColums as $column) {
            $whereStr .= "$column = :$column and ";
        }

        $updateStr = substr($updateStr,0,-2);
        $whereStr = substr($whereStr, 0, -5);

        $sql = sprintf($sql, $this->_table, $updateStr, $whereStr);


        $bind = $update + $where;

        $stmt = $this->execute($sql, $update + $where);

        return $stmt->rowCount();
    }

    public function delete(Array $where)
    {
        $columns = array_keys($where);

        $sql = "delete from %s where %s";
        $whereStr = '';

        foreach ($columns as $column) {
            $whereStr .= "$column = :$column and ";
        }

        $whereStr = substr($whereStr, 0, -5);

        $sql = sprintf($sql, $this->_table, $whereStr);
        $stmt = $this->execute($sql, $where);

        return $stmt->rowCount();
    }

    public function execute($sql, $bind = [])
    {
        $stmt = $this->_db->prepare($sql);

        if(! empty($bind)) {
            foreach ($bind as $key => $item) {
                $stmt->bindValue(":$key", $item);
            }
        }

        $stmt->execute();

        return $stmt;
    }

    public function startTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function commit()
    {
        $this->_db->commit();
    }

    public function rollback()
    {
        $this->_db->rollBack();
    }
}
