<?php

namespace Modules\User\Model;

class Roles extends \Components\Model
{
    protected $_table = 'roles';


    public function add($info)
    {
         return $this->insert($info);
    }

    public function getInfoById($roleId)
    {
        $sql = "select * from " . $this->_table . " where id = :id";

        $bind = ['id' => $roleId];

        return $this->fetchRow($sql, $bind);
    }

    public function getInfoByIds(Array $roleIds)
    {
        $format = function($value) {
            return intval($value);
        };
        
        $roleIdStr = '(' . implode(",", array_map($format, $roleIds)) . ')';

        $sql = "select * from " . $this->_table . " where id in " . $roleIdStr;

        return $this->fetchList($sql);
    }

    public function updateById($roleId, $update)
    {
        $where = ['id' => $roleId];

        return $this->update($update, $where);
    }

    public function deleteById($roleId)
    {
        $where = ['id' => $roleId];

        return $this->delete($where);
    }

    public function getList()
    {
        $sql = "select * from " . $this->_table;

        $list = \Components\Pagination::getPage($sql, $this);

        return $list;
    }

    public function getRoles()
    {
        $sql = "select id,name from " . $this->_table;

        return $this->fetchList($sql);
    }
}