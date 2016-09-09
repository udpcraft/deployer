<?php

namespace Command\Project\Sync;

class InitRemoteDir extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $projectHosts = $request->get('hosts');



        $manager = new \Components\Worker\Manager();

        foreach ($projectHosts as $projectHost) {
            $command = sprintf("ssh %s %s@%s mkdir -p %s/release", \Helper\Utility\Command::disableStrictHostKeyCheckOption(), $projectInfo['sync_user'], long2ip($projectHost['host']), $projectInfo['path']);
            $manager->attach(new \Components\Worker\Command(\Helper\Utility\Command::doSudo($projectInfo['sync_user'], $command)));
        }
        
        while (0 < count($manager)) {
            $res = $manager->listen();
        }

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
