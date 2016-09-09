<?php

namespace Modules\Deploy\Model;

class Project extends \Components\Model
{
    protected $_table = 'project';


    public function add($info)
    {
         return $this->insert($info);
    }

    public function getInfoById($projectId)
    {
        $sql = "select * from " . $this->_table . " where id = :id";

        $bind = ['id' => $projectId];

        return $this->fetchRow($sql, $bind);
    }

    public function getInfoByRoles($projectId, Array $ids)
    {
        $ids = array_map("intval", $ids);

        if (empty($ids)) {
            return [];
        }

        $sql ="select project.* from project left join project_roles on project.id = project_roles.project_id";
        $sql .= " where project.id = " . intval($projectId) . " and project_roles.role_id in (" . implode(",", $ids) . ") limit 1";

        return $this->fetchRow($sql);
    }

    public function getListByIds(Array $ids)
    {
        $ids = array_map("intval", $ids);
        $sql = "select * from " . $this->_table . " where id in (" . implode(",", $ids) . ")";

        return $this->fetchList($sql);
    }

    public function updateById($projectId, $update)
    {
        $where = ['id' => $projectId];

        return $this->update($update, $where);
    }

    public function deleteById($projectId)
    {
        $where = ['id' => $projectId];

        return $this->delete($where);
    }

    public function getList()
    {
        $sql = "select * from " . $this->_table;

        $list = \Components\Pagination::getPage($sql, $this);

        return $list;
    }
}
