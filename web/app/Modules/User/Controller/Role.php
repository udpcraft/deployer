<?php

namespace Modules\User\Controller;

class Role extends \Afanty\Web\Controller
{
    public function indexAction()
    {
        $list = \Modules\User\Model\Roles::getInstance()->getList();

        return $list;
    }

    public function editAction()
    {
        $id = $this->get('id');

        $model = \Modules\User\Model\Roles::getInstance();

        if (! \Afanty\Web\Request\Http::isPost()) {
            $info = $model->getInfoById($id);

            $info['permission'] = json_decode(base64_decode($info['permission']),true);
            $info['permission_list'] = \Components\Permission::getConfig();

            return $info;
        }

        $update = [];

        $update['name'] = $this->post('rolename');
        $update['description'] = $this->post('description');
        $update['permission'] = base64_encode(json_encode($this->post('permission')));
        $update['up_time'] = \Helper\Utility\Datetime::current();

        $res = $model->updateById($id, $update);

        $this->redirect('/user/role');
    }

    public function deleteAction()
    {
        $id = $this->get('id');

        $output['status'] = 0;

        $roles = \Afanty\Web\Request\Http::getSession('user_roles');
        if (in_array($id, $roles)) {
            $output['msg'] = 'can not delete your roles';

            return $output;
        }

        if ($id) {
            \Modules\User\Model\Roles::getInstance()->deleteById($id);

            $output['status'] = 1;
        }

        return $output;
    }


    public function createAction()
    {
        if (! \Afanty\Web\Request\Http::isPost()) {

            return ['permission' => \Components\Permission::getConfig()];
        }

        $info = [];
        $info['name'] = $this->post('rolename');
        $info['description'] = $this->post('description');
        $info['permission'] = base64_encode(json_encode($this->post('permission')));
        $info['in_time'] = \Helper\Utility\Datetime::current();
        $info['up_time'] = \Helper\Utility\Datetime::current();

        if (\Modules\User\Model\Roles::getInstance()->add($info)) {
            $this->redirect('/user/role');
        }
    }
}