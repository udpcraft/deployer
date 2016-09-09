<?php

namespace Command\Project\Sync;

class CompressRelease extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $newRelease = $request->get('new_release');

        $sandboxRelease = \Helper\Project::getSandboxDir($projectInfo, 'release');

        $releaseDir = rtrim($sandboxRelease, '/');
        
        $command = sprintf("tar -czf %s/%s.tar.gz -C %s %s", $releaseDir, $newRelease, $releaseDir, $newRelease);

        \Helper\Utility\Command::system($command);

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
