<?php

namespace Command\Project\Release;

class Snapshoting extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $snapshotBase = \Helper\Project::getSnapshotDir($projectInfo);
        
        if (! is_dir($snapshotBase)) {
            \Helper\Utility\Command::system(sprintf("mkdir -p %s" ,$snapshotBase));
        }

        $sandboxRoot = \Helper\Project::getSandboxDir($projectInfo);
        
        $snapshotCommand = sprintf("rsync -av --delete %s/ %s/", rtrim($sandboxRoot, '/'), rtrim($snapshotBase, '/'));

        \Helper\Utility\Command::system($snapshotCommand, 'create snapshot failed');

        return \Command\Project\Response::getInstance()->output($this);
    }
}
