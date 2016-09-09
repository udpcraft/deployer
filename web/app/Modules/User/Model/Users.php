<?php

namespace Modules\User\Model;

class Users extends \Components\Model
{
    protected $_table = 'users';


    public function add($info)
    {
         return $this->insert($info);
    }

    public function getInfoByName($username)
    {
        $sql = "select * from " . $this->_table . " where name = :name";

        $bind = ['name' => $username];

        return $this->fetchRow($sql, $bind);
    }

    public function getInfoById($userId)
    {
        $sql = "select * from " . $this->_table . " where id = :id";

        $bind = ['id' => $userId];

        return $this->fetchRow($sql, $bind);
    }

    public function updateById($userId, $update)
    {
        $where = ['id' => $userId];

        return $this->update($update, $where);
    }

    public function deleteById($userId)
    {
        $where = ['id' => $userId];

        return $this->delete($where);
    }

    public function getList()
    {
        $sql = "select * from " . $this->_table;

        $list = \Components\Pagination::getPage($sql, $this);

        return $list;
    }
}