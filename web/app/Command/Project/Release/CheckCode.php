<?php

namespace Command\Project\Release;

class CheckCode extends \Command\Project\Handler
{
    protected function _process(\Command\Project\Request $request)
    {
        $projectInfo = $request->get('projectInfo');
        $post = $request->get('posts');

        $reposDir = \Helper\Project::getSandboxDir($projectInfo, 'repos');

        if (! is_dir($reposDir . '/.git')) {
            $this->_checkoutResource($projectInfo['repos_url'], $reposDir, $post['release_type'], $post['release_type_value']);
        }

        $this->_updateResource($reposDir, $post['release_type'], $post['release_type_value']);

        $command = sprintf("cd %s && %s", $reposDir, $this->_formatReleaseType($post['release_type'], $post['release_type_value']));
        
        \Helper\Utility\Command::system($command, 'get tag failed');

        return \Command\Project\Response::getInstance()->output($this);
    }


    private function _checkoutResource($reposUrl, $reposDir, $releaseType, $typeValue)
    {
        $command = sprintf('GIT_SSH="%s" git clone %s %s/', \Helper\Utility\Command::gitSshProxy(), $reposUrl, $reposDir);

        return \Helper\Utility\Command::system($command, 'init code failed');
    }

    private function _formatReleaseType($releaseType, $typeValue)
    {
        if ($releaseType == 1) {
            return "git reset --hard {$typeValue}";
        }

        return "git checkout $typeValue";
    }

    private function _updateResource($reposDir, $releaseType, $typeValue)
    {
        $command = sprintf('cd %s && git checkout master && GIT_SSH="%s" git pull', $reposDir, \Helper\Utility\Command::gitSshProxy());

        return \Helper\Utility\Command::system($command, 'update code failed');
    }
    
}
