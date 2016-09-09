<?php

namespace Modules\Deploy\Controller;

class Release extends \Afanty\Web\Controller
{
    
    public function indexAction()
    {
        $roleIds = \Afanty\Web\Request\Http::getSession('user_roles');

        $list = \Modules\Deploy\Model\ProjectRoles::getInstance()->getListByRoleIds($roleIds);

        if (empty($list['_res'])) {
            return $list;
        }

        $projectIds = array_column($list['_res'], 'project_id');

        unset($list['_res']);
        $list['_res'] = \Modules\Deploy\Model\Project::getInstance()->getListByIds($projectIds);

        return $list;
    }

    public function projectAction()
    {
        $projectId = $this->get('id');

        $projectModel = \Modules\Deploy\Model\Project::getInstance();

        $info = $projectModel->getInfoById($projectId);
        
        if (! \Afanty\Web\Request\Http::isPost()) {
            return $info;
        }

        $lockProject = \Command\Project\Release\LockProject::getInstance();
        $upReleaseType = \Command\Project\Release\UpReleaseType::getInstance();
        $initSandbox = \Command\Project\Release\InitSandbox::getInstance();
        $snapshoting = \Command\Project\Release\Snapshoting::getInstance();
        $checkCode = \Command\Project\Release\CheckCode::getInstance();
        $createTags = \Command\Project\Release\CreateTags::getInstance();

        $lockProject->setSuccessor($upReleaseType)
            ->setSuccessor($initSandbox)
            ->setSuccessor($snapshoting)
            ->setSuccessor($checkCode)
            ->setSuccessor($createTags);

        $request = \Command\Project\Request::getInstance();

        $request->set(['projectId' => $projectId, 'projectInfo' => $info, 'posts' => $this->post('userForm')]);

        try {
            $lockProject->handle($request);
        } catch (\Exception $e) {
            \Helper\Project::endProcessing($info, $e->getMessage());
            \Helper\Project::convertFromSnapshot($info);
        }
        
        //todo
        /* todo
           1 update release type
           2 init release sandbox: 1)repos, 2) tags, 3) release
           3 git check code
           4 synax check
           5 lock project
           6 confirm
         */
        
    }

    public function diffAction()
    {
        $projectId = $this->get('id');

        $projectModel = \Modules\Deploy\Model\Project::getInstance();
        
        $info = $projectModel->getInfoById($projectId);

        if (empty($info['status']) || $info['status'] != 2) {
            throw new \Afanty\Exception\Request('can not diff for [' . $projectId . ']');
        }        

        if (\Afanty\Web\Request\Http::isPost()) {
            if ($this->post('confirm')) {
                $this->_setSyncable($projectId);
            }

            if ($this->post('cancel')) {
                \Helper\Project::convertFromSnapshot($info);
            }

            $this->redirect('/deploy/release');
        }

        $sandboxTags = \Helper\Project::getSandboxDir($info, 'tags');
        $sandboxRelease = \Helper\Project::getSandboxDir($info, 'release');

        if ($info['current_release']) {
             $sandboxRelease = $sandboxRelease . '/' . $info['current_release'];
        }


        $command = "LANG='en_US.UTF-8' diff -r -u -N {$sandboxTags} {$sandboxRelease}";


        $output = \Helper\Utility\Command::system($command, '', false);


        $diff = array();

        foreach ($output as $row) {
            if (strpos($row, 'diff -r -u -N') === 0) {
                continue;
            }

            if (in_array($prefix = substr($row, 0, 4), array('+++ ', '--- '))) {
                if ($prefix == '+++ ') {
                    continue;
                }
                if ($prefix == '--- ') {
                    $script = strstr(substr($row, 4), "\t", true);
                    $script = str_replace($sandboxTags, '', $script);
                    $row = false;
                }
            }

            if (isset($script) && $row) {
                $diff[$script][] = $row;
            }
        }

        return ['diff' => $diff];
 
    }

    private function _setSyncable($projectId)
    {
        $projectModel = \Modules\Deploy\Model\Project::getInstance();
        
        $info = $projectModel->getInfoById($projectId);

        if (empty($info['status'] || $info['status'] != 2)) {
            throw new \Exception("ss");
        }

        $update = ['status' => 3];

        $projectModel->updateById($projectId, $update);
    }

    public function syncAction()
    {
        $projectId = $this->get('id');

        $projectModel = \Modules\Deploy\Model\Project::getInstance();

        $info = $projectModel->getInfoById($projectId);

        if (empty($info['status']) || $info['status'] != 3) {
            //throw new \Afanty\Exception\Request('can not sync project for [' . $projectId . ']');
        }

        $projectHosts = \Modules\Deploy\Model\ProjectHosts::getInstance()->getInfoByProjectId($projectId);

        $truncateRelease = \Command\Project\Sync\TruncateRelease::getInstance();
        $upReleaseName = \Command\Project\Sync\UpReleaseName::getInstance();
        $createRelease = \Command\Project\Sync\CreateRelease::getInstance();
        $compressRelease = \Command\Project\Sync\CompressRelease::getInstance();
        $initRemoteDir = \Command\Project\Sync\InitRemoteDir::getInstance();
        $syncToRemote = \Command\Project\Sync\SyncToRemote::getInstance();
        $uncompressRlease = \Command\Project\Sync\UncompressRelease::getInstance();
        $linkCurrent = \Command\Project\Sync\LinkCurrent::getInstance();
        $cleanup = \Command\Project\Sync\Cleanup::getInstance();

        $truncateRelease->setSuccessor($upReleaseName)
            ->setSuccessor($createRelease)
            ->setSuccessor($compressRelease)
            ->setSuccessor($initRemoteDir)
            ->setSuccessor($syncToRemote)
            ->setSuccessor($uncompressRlease)
            ->setSuccessor($linkCurrent)
            ->setSuccessor($cleanup);
                         
        $request = \Command\Project\Request::getInstance();

        $request->set(['projectId' => $projectId, 'projectInfo' => $info, 'hosts' => $projectHosts]);

        try {
            $truncateRelease->handle($request);
        } catch (\Exception $e) {
            \Helper\Project::endProcessing($info, $e->getMessage());
            \Helper\Project::convertFromSnapshot($info);            
        }

        /*
          todo
          1 get project info
          2 gzip code
          3. backup remote code
          3 scp to remote

          4 mv and link
          5 cleanup
          6 convert with snapshot when failed



          scp:
          1 scp a.zip 192.168.1.200:/opt/web/be/a.Zip
          2 unzip a.zip
          3 ln -sfn a ./aa.tmp
          4 mv -fT aa.tmp aa
         */
    }

    public function rollbackAction()
    {
        /*
          todo 
          1 get project info
          2 
         */
        $projectId = $this->get('id');

        $projectModel = \Modules\Deploy\Model\Project::getInstance();

        $info = $projectModel->getInfoById($projectId);

        $projectHosts = \Modules\Deploy\Model\ProjectHosts::getInstance()->getInfoByProjectId($projectId);

        if (empty($info['status']) || $info['status'] != 4) {
            throw new \Afanty\Exception\Request('can not rollback project for [' . $projectId . ']');
        }

        $relinkRemote = \Command\Project\Rollback\RelinkRemote::getInstance();
        $upReleaseName = \Command\Project\Rollback\UpReleaseName::getInstance();

        $relinkRemote->setSuccessor($upReleaseName);
                         
        $request = \Command\Project\Request::getInstance();

        $request->set(['projectId' => $projectId, 'projectInfo' => $info, 'hosts' => $projectHosts]);

        try {
            $relinkRemote->handle($request);
        } catch (\Exception $e) {
            \Helper\Project::endProcessing($info, $e->getMessage());
        }
        
    }
    
}