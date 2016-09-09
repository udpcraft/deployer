<?php

namespace Command\Project\Sync;

class Cleanup extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $projectHosts = $request->get('hosts');

        $newRelease = $request->get('new_release');

        $this->_cleanLocalRelease($projectInfo, $newRelease);

        $this->_cleanRemote($projectInfo, $projectHosts);

        $this->_cleanLocalSnapshot($projectInfo);

        return \Command\Project\Response::getInstance()->output($this, 2);
    }

    private function _cleanRemote($projectInfo, $projectHosts)
    {
        if (empty($projectInfo['previous_release'])) {
            return true;
        }
        
        $manager = new \Components\Worker\Manager();

        foreach ($projectHosts as $projectHost) {
            $command = sprintf('ssh %s %s@%s "rm -fr %s/release/%s"',\Helper\Utility\Command::disableStrictHostKeyCheckOption(), $projectInfo['sync_user'], long2ip($projectHost['host']), $projectInfo['path'], $projectInfo['previous_release']);
            $manager->attach(new \Components\Worker\Command(\Helper\Utility\Command::doSudo($projectInfo['sync_user'], $command)));
        }

        while (0 < count($manager)) {
            $res = $manager->listen();
        }
        
    }

    private function _cleanLocalRelease($projectInfo, $releaseName)
    {
        $sandboxRelease = \Helper\Project::getSandboxDir($projectInfo, 'release');

        $releaseDir = rtrim($sandboxRelease, '/');

        $command = sprintf("rm -f %s/%s.tar.gz", $releaseDir, $releaseName);

        \Helper\Utility\Command::system($command);

        if (! empty($projectInfo['previous_release'])) {

            $command = sprintf("rm -fr %s/%s", $releaseDir, $projectInfo['previous_release']);

            \Helper\Utility\Command::system($command);
        }
    }

    private function _cleanLocalSnapshot($projectInfo)
    {
        $snapshotDir = \Helper\Project::getSnapshotDir($projectInfo);

        $command = "rm -fr {$snapshotDir}/";

        \Helper\Utility\Command::system($command);
    }
}
