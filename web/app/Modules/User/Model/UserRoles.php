<?php

namespace Modules\User\Model;

class UserRoles extends \Components\Model
{
    protected $_table = 'user_roles';


    public function add($info)
    {
        return $this->insert($info);
    }

    public function getRoleIdsByUserId($userId)
    {
        $sql = "select * from " . $this->_table . " where user_id = :user_id";

        $bind = ['user_id' => $userId];

        $res = $this->fetchList($sql, $bind);

        return array_column($res, 'role_id');
    }

    public function deleteByUserId($userId)
    {
        $where = ['user_id' => $userId];

        return $this->delete($where);
    }
}