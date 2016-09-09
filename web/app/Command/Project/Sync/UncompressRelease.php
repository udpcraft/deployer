<?php

namespace Command\Project\Sync;

class UncompressRelease extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $projectHosts = $request->get('hosts');

        $newRelease = $request->get('new_release');

        $manager = new \Components\Worker\Manager();

        foreach ($projectHosts as $projectHost) {
            $tarFileName = sprintf("%s/release/%s.tar.gz", $projectInfo['path'], $newRelease);
            $command = sprintf('ssh %s %s@%s "cd %s/release && tar -zxf %s && rm -f %s"', \Helper\Utility\Command::disableStrictHostKeyCheckOption(), $projectInfo['sync_user'], long2ip($projectHost['host']), $projectInfo['path'], $tarFileName, $tarFileName);

            $manager->attach(new \Components\Worker\Command(\Helper\Utility\Command::doSudo($projectInfo['sync_user'], $command)));
        }
        
        while (0 < count($manager)) {
            $res = $manager->listen();
        }

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
