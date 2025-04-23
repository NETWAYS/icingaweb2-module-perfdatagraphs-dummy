<?php

namespace Icinga\Module\Perfdatagraphsdummy\Generator;

use Icinga\Module\Perfdatagraphs\Model\PerfdataRequest;
use Icinga\Module\Perfdatagraphs\Model\PerfdataResponse;
use Icinga\Module\Perfdatagraphs\Model\PerfdataSet;
use Icinga\Module\Perfdatagraphs\Model\PerfdataSeries;

use DateInterval;
use DateTimeImmutable;
use Exception;

class Error
{
    public $count = 60;

    protected function outOfMemory(): void
    {
        ini_set('memory_limit', '32M');
        $data = [];
        while (true) {
            $data[] = str_repeat('A', 1024 * 1024);
        }
    }

    protected function generateRandomInvalidData(int $case = 0): PerfdataResponse
    {
        if ($case === 0) {
            $case = rand(1, 12);
        }

        $pdr = new PerfdataResponse();

        switch ($case) {
            case 1:
                $d = $this->outOfMemory();
                break;
            case 2:
                return $pdr;
            break;
            case 3:
                $ds = new PerfdataSet('just a title');
                $pdr->addDataset($ds);
                break;
            case 4:
                $ds = new PerfdataSet('empty timestamps');
                $pdr->addDataset($ds);
                break;
            case 5:
                $ds = new PerfdataSet('empty timestamps');
                $pdr->addDataset($ds);
                break;
            case 6:
                $ds = new PerfdataSet('no dataseries');
                $s1 = new PerfdataSeries('nothing here', []);
                $ds->addSeries($s1);
                $pdr->addDataset($ds);
                break;
            case 7:
                $pdr->addError('something not right');
                break;
            case 8:
                $pdr->addError('one thing not right');
                $pdr->addError('another thing not right');
                break;
            default:
                throw new Exception('¯\_(ツ)_/¯');
            break;
        }

        return $pdr;
    }

    public function generate(PerfdataRequest $rep): PerfdataResponse
    {
        $now = new DateTimeImmutable();

        $int = new DateInterval('PT12H');

        $pdr = new PerfdataResponse();

        try {
            $int = new DateInterval($duration);
        } catch (Exception $e) {
            $pdr->addError('Failed to parse date interval: %s', $e);
        }

        $diff = $now->sub($int);

        $seconds = $now->getTimestamp() - $diff->getTimestamp();
        $c = $seconds / 600;

        if ($c > 1200) {
            $c = 1000;
        }

        $this->count = $c;

        return $this->generateRandomInvalidData();
    }
}
