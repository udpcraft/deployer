<?php

namespace Command\Project\Release;

class UpReleaseType extends \Command\Project\Handler
{

    protected function _process(\Command\Project\Request $request)
    {
        $projectId = $request->get('projectId');

        $posts = $request->get('posts');

        $update['release_type'] = $posts['release_type'];

        \Modules\Deploy\Model\Project::getInstance()->updateById($projectId, $update);

        return \Command\Project\Response::getInstance()->output($this);
    }
}
