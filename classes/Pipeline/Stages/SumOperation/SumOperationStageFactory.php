<?php

namespace Pipeline\Stages\SumOperation;

require_once "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once "classes/Pipeline/Utils/Helpers.php";
require_once "classes/Pipeline/Stages/SumOperation/SumOperationStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SumOperation\SumOperationStage;
use Pipeline\Utils\Helpers;

class SumOperationStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        return SumOperationStage::getDescriptor();
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $parameterA = Helpers::getField($configuration, "parameterA", true);
        $parameterB = Helpers::getField($configuration, "parameterB", true);
        $resultParameter = Helpers::getField($configuration, "resultParameter", true);
        return new SumOperationStage($parameterA, $parameterB, $resultParameter);
    }
}