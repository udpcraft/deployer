<?php

namespace Modules\Deploy\Model;

class ProjectHosts extends \Components\Model
{
    protected $_table = 'project_hosts';


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
}