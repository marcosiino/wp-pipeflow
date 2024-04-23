<?php

namespace Pipeline\Stages\SetValue;

require_once "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once "classes/Pipeline/Utils/Helpers.php";
require_once "classes/Pipeline/Stages/SetValue/SetValueStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SetValue\SetValueStage;
use Pipeline\Utils\Helpers;

class SetValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        return SetValueStage::getDescriptor();
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $parameterName = Helpers::getField($configuration, "parameterName", true);
        $parameterValue = Helpers::getField($configuration, "parameterValue", true);
        return new SetValueStage($parameterName, $parameterValue);
    }
}