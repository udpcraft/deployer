<?php

namespace Modules\Deploy\Controller;

class Project extends \Afanty\Web\Controller
{
    public function indexAction()
    {
        $list = \Modules\Deploy\Model\Project::getInstance()->getList();

        return $list;
    }

    public function editAction()
    {
        $projectId = $this->get('id');

        \Helper\Project::checkAuth($projectId);

        $projectModel = \Modules\Deploy\Model\Project::getInstance();
        $projectHostModel = \Modules\Deploy\Model\ProjectHosts::getInstance();
        $projectRoleModel = \Modules\Deploy\Model\ProjectRoles::getInstance();

        $authProjectInfo = $projectModel->getInfoByRoles($projectId, \Afanty\Web\Request\Http::getSession('user_roles'));

        if (! \Afanty\Web\Request\Http::isPost()) {
            $info = $projectModel->getInfoById($projectId);
            
            $info['hosts'] = $projectHostModel->getInfoByProjectId($projectId);
            $info['roles'] = $projectRoleModel->getInfoByProjectId($projectId);

            $info['hosts'] = empty($info['hosts']) ? [] : array_column($info['hosts'], 'port', 'host');

            $info['port'] = current($info['hosts']);

            $info['hosts'] = array_keys($info['hosts']);

            $info['hosts'] = array_map('long2ip', $info['hosts']);

            $info['hostStr'] = implode("\r\n", $info['hosts']);

            $info['roles'] = empty($info['roles']) ? [] : array_column($info['roles'], 'role_id');

            $info['role_lists'] = \Modules\user\Model\Roles::getInstance()->getRoles();

            return $info;
        }


        $projectInfo = $this->post('userForm');

        $hosts = explode("\r\n", $projectInfo['hosts']);
        $port  = $projectInfo['port'];

        unset($projectInfo['hosts']);
        unset($projectInfo['port']);

        $projectInfo['up_time'] = \Helper\Utility\Datetime::current();

        $res = $projectModel->updateById($projectId, $projectInfo);

        //clean old host and roles
        $projectHostModel->deleteByProjectId($projectId);
        $projectRoleModel->deleteByProjectId($projectId);

        //todo batch insert

        $roles = $this->post('roleForm');

        foreach ($roles['role'] as $roleId) {
            $info = [
                'project_id' => $projectId,
                'role_id' => $roleId,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $projectRoleModel->add($info);
        }

        foreach ($hosts as $host) {
            if (empty($host)) {
                continue;
            }
            $info = [
                'project_id' => $projectId,
                'host' => ip2long(trim($host)),
                'port' => $port,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $projectHostModel->add($info);
        }

        $this->redirect('/deploy/project');
    }

    public function deleteAction()
    {
        $id = $this->get('id');

        $output['status'] = 0;

        if ($id) {
            $projectInfo = \Helper\Project::checkAuth($id);
            
            \Modules\Deploy\Model\Project::getInstance()->deleteById($id);
            \Modules\Deploy\Model\ProjectHosts::getInstance()->deleteByProjectId($id);
            \Modules\Deploy\Model\ProjectRoles::getInstance()->deleteByProjectId($id);

            \Helper\Project::cleanDeploy($projectInfo);

            $output['status'] = 1;
        }

        return $output;
    }


    public function createAction()
    {
        $roleModel = \Modules\User\Model\Roles::getInstance();

        if (! \Afanty\Web\Request\Http::isPost()) {
            return ['roles' => $roleModel->getRoles()];
        }

        $userRolesModel = \Modules\User\Model\UserRoles::getInstance();

        $projectModel = \Modules\Deploy\Model\Project::getInstance();
        $projectHostModel = \Modules\Deploy\Model\ProjectHosts::getInstance();
        $projectRoleModel = \Modules\Deploy\Model\ProjectRoles::getInstance();


        $projectInfo = $this->post('userForm');

        $hosts = explode("\r\n", $projectInfo['hosts']);
        $port  = $projectInfo['port'];

        unset($projectInfo['hosts']);
        unset($projectInfo['port']);

        $projectInfo['in_time'] = \Helper\Utility\Datetime::current();
        $projectInfo['up_time'] = \Helper\Utility\Datetime::current();

        $projectId = $projectModel->add($projectInfo);


        //todo batch insert

        $roles = $this->post('roleForm');

        foreach ($roles['role'] as $roleId) {
            $info = [
                'project_id' => $projectId,
                'role_id' => $roleId,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $projectRoleModel->add($info);
        }

        foreach ($hosts as $host) {
            if (empty($host)) {
                continue;
            }
            $info = [
                'project_id' => $projectId,
                'host' => ip2long(trim($host)),
                'port' => $port,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $projectHostModel->add($info);
        }

        $this->redirect('/deploy/project');
    }

}