<?php

namespace Command\Project\Sync;

class CreateRelease extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $newRelease = $request->get('new_release');

        $sandboxTags = \Helper\Project::getSandboxDir($projectInfo, 'tags');
        $sandboxRelease = \Helper\Project::getSandboxDir($projectInfo, 'release');
        
        $command = sprintf("rsync -av --delete %s/ %s/%s/", rtrim($sandboxTags, '/'), rtrim($sandboxRelease, '/'), $newRelease);

        \Helper\Utility\Command::system($command);

        return \Command\Project\Response::getInstance()->output($this);
    }

}
