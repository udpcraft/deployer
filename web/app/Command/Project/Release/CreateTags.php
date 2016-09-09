<?php

namespace Command\Project\Release;

class CreateTags extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $sandboxRepos = \Helper\Project::getSandboxDir($projectInfo, 'repos');
        $sandboxTags = \Helper\Project::getSandboxDir($projectInfo, 'tags');
        
        $command = sprintf("rsync -av --delete %s/ %s/", rtrim($sandboxRepos, '/'), rtrim($sandboxTags, '/'));

        \Helper\Utility\Command::system($command, 'create tag failed');

        $cleanupCommand = sprintf("rm -fr %s/%s", rtrim($sandboxTags, '/'), '.git');

        \Helper\Utility\Command::system($cleanupCommand);

        // update project status, set to 2 show diffAction

        $update = ['status' => 2];

        \Modules\Deploy\Model\Project::getInstance()->updateById($request->get('projectId'), $update);

        return \Command\Project\Response::getInstance()->output($this, 2);
    }

    
}
