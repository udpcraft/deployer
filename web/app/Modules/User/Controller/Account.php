<?php

namespace Modules\User\Controller;

class Account extends \Afanty\Web\Controller
{
    public function indexAction()
    {
        $list = \Modules\User\Model\Users::getInstance()->getList();

        return $list;
    }

    public function editAction()
    {
        $userId = $this->get('id');

        $model = \Modules\User\Model\Users::getInstance();

        if (! \Afanty\Web\Request\Http::isPost()) {
            $info = $model->getInfoById($userId);
            $info['roles'] = \Modules\user\Model\Roles::getInstance()->getRoles();
            $info['current_roles'] = \Modules\User\Model\UserRoles::getInstance()->getRoleIdsByUserId($userId);

            return $info;
        }

        $userRolesModel = \Modules\User\Model\UserRoles::getInstance();

        // update user info
        $userInfo = $this->post('userForm');
        $userInfo['password'] = md5(md5($userInfo['password']));
        $userInfo['up_time'] = \Helper\Utility\Datetime::current();
        $res = $model->updateById($userId, $userInfo);

        // clean old user role mapping
        $userRolesModel->deleteByUserId($userId);

        // add user role mapping
        $roles = $this->post('roleForm');

        foreach ($roles['role'] as $roleId) {
            $info = [
                'user_id' => $userId,
                'role_id' => $roleId,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $userRolesModel->add($info);
        }

        $this->redirect('/user/account');
    }

    public function deleteAction()
    {
        $userId = $this->get('id');

        $output['status'] = 0;
        
        if ($userId == \Afanty\Web\Request\Http::getSession('user_info')['uid']) {
            $output['msg'] = 'can not delete your self';

            return $output;
        }

        if ($userId) {
            \Modules\User\Model\Users::getInstance()->deleteById($userId);
            \Modules\User\Model\UserRoles::getInstance()->deleteByUserId($userId);
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

        $accountModel = \Modules\User\Model\Users::getInstance();
        $userRolesModel = \Modules\User\Model\UserRoles::getInstance();

        $userInfo = $this->post('userForm');
        $userInfo['password'] = md5(md5($userInfo['password']));
        $userInfo['in_time'] = \Helper\Utility\Datetime::current();
        $userInfo['up_time'] = \Helper\Utility\Datetime::current();

        $userId = $accountModel->add($userInfo);

        $roles = $this->post('roleForm');

        foreach ($roles['role'] as $roleId) {
            $info = [
                'user_id' => $userId,
                'role_id' => $roleId,
                'in_time' => \Helper\Utility\Datetime::current(),
            ];

            $userRolesModel->add($info);
        }

        $this->redirect('/user/account');
    }

}