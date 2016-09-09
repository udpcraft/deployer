<?php

namespace Command\Project\Sync;

class UpReleaseName extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');

        $releaseName = time();

        $update = [
            'current_release' => $releaseName,
            'status' => 2,
        ];

        if (! empty($projectInfo['current_release'])) {
            $update['previous_release'] = $projectInfo['current_release'];
            $update['status'] = 4;
        }

        \Modules\Deploy\Model\Project::getInstance()->updateById($projectInfo['id'], $update);

        $request->set(['new_release' => $releaseName]);

        return \Command\Project\Response::getInstance()->output($this);
    }
}
