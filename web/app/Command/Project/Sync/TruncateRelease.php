<?php

namespace Command\Project\Sync;

class TruncateRelease extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $sandboxRelease = \Helper\Project::getSandboxDir($projectInfo, 'release');
        
        $command = sprintf("rm -fr %s/*", rtrim($sandboxRelease, '/'));

        \Helper\Utility\Command::system($command);

        return \Command\Project\Response::getInstance()->output($this);
    }

    
}
