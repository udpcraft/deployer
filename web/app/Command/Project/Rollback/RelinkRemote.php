<?php

namespace Command\Project\Rollback;

class RelinkRemote extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $projectHosts = $request->get('hosts');

        $manager = new \Components\Worker\Manager();

        foreach ($projectHosts as $projectHost) {
            $command = sprintf('ssh %s %s@%s "ln -sfn %s/release/%s %s/current.tmp && mv -fT %s/current.tmp %s/current"', \Helper\Utility\Command::disableStrictHostKeyCheckOption(), $projectInfo['sync_user'], long2ip($projectHost['host']), $projectInfo['path'], $projectInfo['previous_release'], $projectInfo['path'], $projectInfo['path'], $projectInfo['path']);
            $manager->attach(new \Components\Worker\Command(\Helper\Utility\Command::doSudo($projectInfo['sync_user'], $command)));
        }        
        
        while (0 < count($manager)) {
            $res = $manager->listen();
        }

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
