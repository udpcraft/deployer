<?php

namespace Command\Project\Sync;

class SyncToRemote extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $projectHosts = $request->get('hosts');

        $newRelease = $request->get('new_release');

        $sandboxRelease = \Helper\Project::getSandboxDir($projectInfo, 'release');

        $releaseDir = rtrim($sandboxRelease, '/');
        
        $manager = new \Components\Worker\Manager();

        foreach ($projectHosts as $projectHost) {
            $command = sprintf("scp %s -r %s/%s.tar.gz %s@%s:%s/release/", \Helper\Utility\Command::disableStrictHostKeyCheckOption(), $releaseDir, $newRelease, $projectInfo['sync_user'], long2ip($projectHost['host']), $projectInfo['path']);


            $manager->attach(new \Components\Worker\Command(\Helper\Utility\Command::doSudo($projectInfo['sync_user'], $command)));
        }
        
        while (0 < count($manager)) {
            $res = $manager->listen();
        }

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
