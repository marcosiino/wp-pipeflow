<?php

namespace tests;
require_once "classes/Pipeline/PipelineContext.php";
require_once "classes/Pipeline/PromptProcessor.php";

use PHPUnit\Framework\TestCase;
use Pipeline\PipelineContext;
use Pipeline\PromptProcessor;

class PromptProcessorTest extends TestCase
{
    public function testBasicPlaceholders(): void {
        $context = new PipelineContext();
        $context->setParameter("FRUITS", "apple");
        $context->setParameter("FRUITS", "pear");
        $context->setParameter("VEGETABLES", "tomato");

        $prompt = "Take a %%FRUITS%% and a %%VEGETABLES%%";

        $promptProcessor = new PromptProcessor($context);
        $result = $promptProcessor->process($prompt);
        $this->assertSame($result, "Take a pear and a tomato");
    }

    public function testArrayPlaceholders(): void {
        $context = new PipelineContext();
        $context->setParameter("NUMBERS", "One");
        $context->setParameter("NUMBERS", "Two");
        $context->setParameter("NUMBERS", "Three");
        $context->setParameter("NUMBERS", "Four");
        $context->setParameter("NUMBERS", "Five");

        $prompt = "I chose the number: %%NUMBERS[1]%%";

        $promptProcessor = new PromptProcessor($context);
        $result = $promptProcessor->process($prompt);
        $this->assertSame($result, "I chose the number: Two");
    }

}