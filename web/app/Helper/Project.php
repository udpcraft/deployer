<?php

namespace Helper;


class Project
{
    public static $reposType = [
        'repos',
        'tags',
        'release'
    ];

    public static function getEnv($id = '')
    {
        $mapping = [
            1 => 'test',
            2 => 'pre-online',
            3 => 'product',
        ];

        if (! $id) {
            return $mapping;
        }

        return empty($mapping[$id]) ? '' : $mapping[$id];
    }

    public static function getVcs($id = '')
    {
        $mapping = [
            1 => 'git',
            2 => 'svn',
        ];

        if (! $id) {
            return $mapping;
        }

        return empty($mapping[$id]) ? '' : $mapping[$id];
    }

    public static function getDeployPath($path)
    {
        return sprintf("%s/current", $path);
    }

    public static function getReleaseType($id = '')
    {
        $mapping = [
            1 => 'tag',
            2 => 'branch',
        ];

        if (! $id) {
            return $mapping;
        }

        return empty($mapping[$id]) ? '' : $mapping[$id];
    }

    public static function getGroup($id = '')
    {
        $mapping = [
            1 => 'be',
            2 => 'bg',
            3 => 'fe',
        ];

        if (! $id) {
            return $mapping;
        }

        return empty($mapping[$id]) ? '' : $mapping[$id];
    }

    public static function upProcessing($projectInfo, $content)
    {
        $processingFile = sprintf("%s/processing", self::getLogDir($projectInfo));
        file_put_contents($processingFile, json_encode($content));
    }

    public static function getProcessing($projectInfo)
    {
        $processingFile = sprintf("%s/processing", self::getLogDir($projectInfo));
        if (file_exists($processingFile)) {
            return json_decode(file_get_contents($processingFile), true);
        }
        return ['status' => 0];
    }

    public static function endProcessing($projectInfo, $msg)
    {
        self::upProcessing($projectInfo, ['status' => 0, 'msg' => $msg]);
    }

    public static function getSandboxDir($projectInfo, $type = '')
    {
        $repositoryRoot = self::_getRepositoryRoot();

        if (! $type) {
            return sprintf('%s/%s/%s/%s', $repositoryRoot, 'sandbox', \Helper\Project::getGroup($projectInfo['groups']), $projectInfo['name']);
        }

        if (! in_array($type, self::$reposType)) {
            throw new \Exception("unknow repos type [$type]");
        }

        return sprintf('%s/%s/%s/%s/%s', $repositoryRoot, 'sandbox', \Helper\Project::getGroup($projectInfo['groups']), $projectInfo['name'], $type);

    }

    public static function getSnapshotDir($projectInfo, $type = '')
    {
        $repositoryRoot = self::_getRepositoryRoot();

        if (! $type) {
            return sprintf('%s/%s/%s/%s', $repositoryRoot, 'snapshot', \Helper\Project::getGroup($projectInfo['groups']), $projectInfo['name']);
        }

        if (! in_array($type, self::$reposType)) {
            throw new \Exception("unknow repos type [$type]");
        }

        return sprintf('%s/%s/%s/%s/%s', $repositoryRoot,'snapshot', \Helper\Project::getGroup($projectInfo['groups']), $projectInfo['name'], $type);

    }

    public static function getLogDir($projectInfo)
    {
        $repositoryRoot = self::_getRepositoryRoot();

        return sprintf('%s/%s/%s/%s', $repositoryRoot, 'var', \Helper\Project::getGroup($projectInfo['groups']), $projectInfo['name']);

    }

    private static function _getRepositoryRoot()
    {
        return sprintf("%s/%s", dirname(\Afanty\Config\Env::getRootDir()), 'envs/deploy');
    }

    public static function convertFromSnapshot($projectInfo)
    {
        $snapshotDir = self::getSnapshotDir($projectInfo);
        $sandboxDir = self::getSandboxDir($projectInfo);

        $command = sprintf("rsync -av --delete %s/ %s/", rtrim($snapshotDir, '/'), rtrim($sandboxDir, '/'));

        \Helper\Utility\Command::system($command);

        \Modules\Deploy\Model\Project::getInstance()->updateById($projectInfo['id'], ['status' => 1]);
    }

    public static function checkAuth($projectId)
    {
        $res = \Modules\Deploy\Model\Project::getInstance()->getInfoByRoles($projectId, \Afanty\Web\Request\Http::getSession('user_roles'));

        if (empty($res)) {
            throw new \Afanty\Exception\Request("unauthed project [$projectId]");
        }

        return $res;
    }

    public static function cleanDeploy($projectInfo)
    {
        $snapshotDir = self::getSnapshotDir($projectInfo);
        $sandboxDir = self::getSandboxDir($projectInfo);
        $cleanSnapshotCommand = sprintf("rm -fr %s", rtrim($snapshotDir, '/'));

        \Helper\Utility\Command::system($cleanSnapshotCommand);

        $cleanSandboxCommand = sprintf("rm -fr %s", rtrim($sandboxDir, '/'));

        \Helper\Utility\Command::system($cleanSandboxCommand);
    }
}