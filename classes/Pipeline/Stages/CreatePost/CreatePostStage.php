<?php

namespace Pipeline\Stages\CreatePost;

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;

class CreatePostStage extends AbstractPipelineStage
{

    private string $publishStatus;
    private string $authorId;
    /**
     * @inheritDoc
     */
    public function execute(PipelineContext $context): PipelineContext
    {
        return $context;
    }
}