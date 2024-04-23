<?php

namespace Pipeline\Stages\TestStage;

require_once "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once "classes/Pipeline/Utils/Helpers.php";
require_once "classes/Pipeline/Stages/TestStage/TestStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Helpers;
use Pipeline\Stages\TestStage\TestStage;

class TestStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        return TestStage::getDescriptor();
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $prompt = Helpers::getField($configuration, "prompt", true);
        return new TestStage($prompt);
    }
}