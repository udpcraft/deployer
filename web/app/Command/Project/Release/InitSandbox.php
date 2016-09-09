<?php

namespace Command\Project\Release;

class InitSandbox extends \Command\Project\Handler
{

    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');
        
        foreach (\Helper\Project::$reposType as $type) {
            $dir = \Helper\Project::getSandboxDir($projectInfo, $type);
            if (! is_dir($dir)) {
                \Helper\Utility\Command::system(sprintf("mkdir -p %s" ,$dir));
            }
        }

        return \Command\Project\Response::getInstance()->output($this);
    }
}
