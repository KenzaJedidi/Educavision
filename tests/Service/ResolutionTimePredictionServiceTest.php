<?php
namespace App\Tests\Service;

use App\Service\ResolutionTimePredictionService;
use PHPUnit\Framework\TestCase;

class ResolutionTimePredictionServiceTest extends TestCase
{
    public function testPredictUrgentKeywordIsFast()
    {
        $s = new ResolutionTimePredictionService();
        $h = $s->predict('Ceci est urgent, veuillez résoudre tout de suite');
        $this->assertLessThanOrEqual(4, $h);
    }

    public function testPredictLongTextIsLonger()
    {
        $s = new ResolutionTimePredictionService();
        $short = $s->predict('Court');
        $long = $s->predict(str_repeat('lignes ', 2000));
        $this->assertGreaterThanOrEqual($short, $long);
    }
}
