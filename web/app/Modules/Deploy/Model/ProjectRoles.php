<?php

namespace Modules\Deploy\Model;

class ProjectRoles extends \Components\Model
{
    protected $_table = 'project_roles';


    public function add($info)
    {
        return $this->insert($info);
    }

    public function getInfoByProjectId($projectId)
    {
        $sql = "select * from " . $this->_table . " where project_id = :project_id";

        $bind = ['project_id' => $projectId];

        $res = $this->fetchList($sql, $bind);

        return $res;
    }

    public function deleteByProjectId($projectId)
    {
        $where = ['project_id' => $projectId];

        return $this->delete($where);
    }

    public function getListByRoleIds($roleIds)
    {
        if (empty($roleIds)) {
            return [];
        }
        $sql = "select distinct project_id from " . $this->_table . ' where role_id in (' . implode(',', $roleIds) . ')';

        $list = \Components\Pagination::getPage($sql, $this);

        return $list;        
    }    
}