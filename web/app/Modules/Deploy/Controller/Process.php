<?php

namespace Modules\Deploy\Controller;

class Process extends \Afanty\Web\Controller
{

    public function releaseAction()
    {
        $id = $this->get('id');

        $projectModel = \Modules\Deploy\Model\Project::getInstance();

        $info = $projectModel->getInfoById($id);

        if (empty($info)) {
             throw new \Afanty\Exception\Request('unkonow project [' . $id . ']');
        }        
        //todo check project Permission

        return \Helper\Project::getProcessing($info);
    }

}