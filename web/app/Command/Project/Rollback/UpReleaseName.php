<?php

namespace Command\Project\Rollback;

class UpReleaseName extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');


        $update = [
            'current_release' => $projectInfo['previous_release'],
            'previous_release' => $projectInfo['current_release'],
            'status' => 2,
        ];

        \Modules\Deploy\Model\Project::getInstance()->updateById($projectInfo['id'], $update);

        return \Command\Project\Response::getInstance()->output($this, 2);
    }
}
