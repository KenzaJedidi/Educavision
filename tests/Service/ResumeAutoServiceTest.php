<?php
namespace App\Tests\Service;

use App\Service\ResumeAutoService;
use PHPUnit\Framework\TestCase;

class ResumeAutoServiceTest extends TestCase
{
    public function testSummarizeShortTextReturnsSame()
    {
        $s = new ResumeAutoService();
        $text = 'Ceci est un petit texte.';
        $this->assertStringContainsString('Ceci est', $s->summarize($text, 10));
    }

    public function testSummarizeLongTextProducesEllipsis()
    {
        $s = new ResumeAutoService();
        $text = str_repeat('Mot ', 200);
        $summary = $s->summarize($text, 10);
        $this->assertNotEmpty($summary);
        $this->assertStringContainsString('...', $summary);
    }
}
