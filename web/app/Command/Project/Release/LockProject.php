<?php

namespace Command\Project\Release;

class LockProject extends \Command\Project\Handler
{
    const TIMEOUT_MINUTE = 5;

    protected function _process(\Command\Project\Request $request)
    {
        // set locak
        // set project status
        $projectInfo = $request->get('projectInfo');

        $logDir = \Helper\Project::getLogDir($projectInfo);

        if (! is_dir($logDir)) {
            \Helper\Utility\Command::system("mkdir -p $logDir");
        }

        $content = [
            'user' => \Afanty\Web\Request\Http::getSession('user_info')['name'],
            'expire_at' => time() + (self::TIMEOUT_MINUTE * 60),
        ];

        file_put_contents(sprintf("%s/lock", $logDir), json_encode($content));

        //return 'lock project end';
        return \Command\Project\Response::getInstance()->output($this);        
    }
}
