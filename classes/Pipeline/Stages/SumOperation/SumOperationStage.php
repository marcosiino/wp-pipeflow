<?php

namespace Pipeline\Stages\SumOperation;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageConfiguration\StageConfiguration;
use Pipeline\StageDescriptor;

class SumOperationStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $operandA = $this->stageConfiguration->getSettingValue("operandA", $context, true);
        $operandB = $this->stageConfiguration->getSettingValue("operandB", $context, true);
        $resultParameter = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "SUM_RESULT");

        //Both operands are scalars
        if(!is_array($operandA) && !is_array($operandB)) {
            if(is_string($operandA) || is_string($operandB)) {
                // A concatenation is performed
                $context->setParameter($resultParameter, $operandA . $operandB);
            }
            else {
                // A sum is performed
                $context->setParameter($resultParameter, $operandA + $operandB);
            }
        }
        //Both operands are arrays
        else if(is_array($operandA) && is_array($operandB)) {
            $context->setParameter($resultParameter, array_merge($operandA, $operandB));
        }
        //operandA is an array and the operandB is a scalar: adds operandB to the array in operandA
        else if(is_array($operandA) && !is_array($operandB)) {
            $result = $operandA;
            $result[] = $operandB;
            $context->setParameter($resultParameter, $result);
        }
        //operandB is an array and the operandA is a scalar: adds operandB to the array in operandA
        else if(is_array($operandB) && !is_array($operandA)) {
            $result = $operandB;
            $result[] = $operandA;
            $context->setParameter($resultParameter, $result);
        }

        return $context;
    }
}