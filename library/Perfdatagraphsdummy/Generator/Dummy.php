<?php

namespace Icinga\Module\Perfdatagraphsdummy\Generator;

use Icinga\Module\Perfdatagraphs\Model\PerfdataRequest;
use Icinga\Module\Perfdatagraphs\Model\PerfdataResponse;
use Icinga\Module\Perfdatagraphs\Model\PerfdataSet;
use Icinga\Module\Perfdatagraphs\Model\PerfdataSeries;

use DateInterval;
use DateTimeImmutable;
use Exception;

class Dummy
{
    public $count = 60;

    protected function generateFixedData($value = 50): array
    {
        $a = [];

        for ($i = 0; $i < $this->count; $i++) {
            $a[] = $value;
        }

        return $a;
    }

    protected function generateData($min = 0, $max = 100): array
    {
        $a = [];

        for ($i = 0; $i < $this->count; $i++) {
            $a[] = rand($min, $max);
        }

        return $a;
    }

    protected function generateTimestamps(): array
    {
        $timestamps = [];
        $currentTimestamp = time();

        for ($i = 0; $i < $this->count; $i++) {
            $timestamps[] = $currentTimestamp;
            $currentTimestamp += round(log($this->count) * 100);
        }

        return $timestamps;
    }

    protected function generateMuchData()
    {
        $pdr = new PerfdataResponse();

        $timestamps = $this->generateTimestamps();

        for ($i = 0; $i < 20; $i++) {
            $ds = new PerfdataSet('Example Data' . $i);
            $ds->setTimestamps($timestamps);
            $v = new PerfdataSeries('value', $this->generateData(0, 3));
            $ds->addSeries($v);
            $pdr->addDataset($ds);
        }

        return $pdr;
    }

    protected function generateOtherData()
    {
        $pdr = new PerfdataResponse();

        $timestamps = $this->generateTimestamps();

        $ds1 = new PerfdataSet('foobar');
        $ds1->setTimestamps($timestamps);

        $v = new PerfdataSeries('value', $this->generateData(60, 90));
        $warn = new PerfdataSeries('warning', $this->generateFixedData(20));
        $crit = new PerfdataSeries('critical', $this->generateFixedData(40));

        $ds1->addSeries($v);
        $ds1->addSeries($warn);
        $ds1->addSeries($crit);

        $ds2 = new PerfdataSet('barfoo');
        $ds2->setTimestamps($timestamps);

        $v = new PerfdataSeries('value', $this->generateData(60, 90));
        $warn = new PerfdataSeries('warning', $this->generateFixedData(20));
        $crit = new PerfdataSeries('critical', $this->generateFixedData(40));

        $ds2->addSeries($v);
        $ds2->addSeries($warn);
        $ds2->addSeries($crit);

        $pdr->addDataset($ds1);
        $pdr->addDataset($ds2);

        return $pdr;
    }

    protected function generateDiskData()
    {
        $pdr = new PerfdataResponse();

        $timestamps = $this->generateTimestamps();

        $ds1 = new PerfdataSet('foobar', 'bytes');
        $ds1->setTimestamps($timestamps);

        $v = new PerfdataSeries('value', $this->generateData(9000000000, 900000000000000));

        $ds1->addSeries($v);

        $ds2 = new PerfdataSet('barfoo', 'bytes');
        $ds2->setTimestamps($timestamps);

        $v = new PerfdataSeries('value', $this->generateData(60, 90));

        $ds2->addSeries($v);

        $pdr->addDataset($ds1);
        $pdr->addDataset($ds2);

        return $pdr;
    }

    protected function generatePingData()
    {
        $pdr = new PerfdataResponse();

        $timestamps = $this->generateTimestamps();

        $ds1 = new PerfdataSet('pl', 'percentage');
        $ds1->setTimestamps($timestamps);

        // $v = new PerfdataSeries('value', $this->generateData(10, 500));
        $v1 = new PerfdataSeries('value', $this->generateData(0, 100));
        $warn1 = new PerfdataSeries('warning', $this->generateFixedData(20));
        $crit1 = new PerfdataSeries('critical', $this->generateFixedData(40));

        $ds1->addSeries($v1);
        $ds1->addSeries($warn1);
        $ds1->addSeries($crit1);

        $ds2 = new PerfdataSet('rta', 'seconds');
        $ds2->setTimestamps($timestamps);

        $v2 = new PerfdataSeries('value', $this->generateData(0, 100));
        $warn2 = new PerfdataSeries('warning', $this->generateFixedData(20));
        $crit2 = new PerfdataSeries('critical', $this->generateFixedData(40));

        $ds2->addSeries($v2);
        $ds2->addSeries($warn2);
        $ds2->addSeries($crit2);

        $pdr->addDataset($ds1);
        $pdr->addDataset($ds2);

        return $pdr;
    }

    protected function generateLoadData()
    {
        $pdr = new PerfdataResponse();

        $timestamps = $this->generateTimestamps();

        $ds1 = new PerfdataSet('load');
        $ds1->setTimestamps($timestamps);

        $load1 = new PerfdataSeries('load1', $this->generateData(0, 3));
        $load5 = new PerfdataSeries('load5', $this->generateData(0, 5));
        $load15 = new PerfdataSeries('load15', $this->generateData(0, 15));

        $ds1->addSeries($load1);
        $ds1->addSeries($load5);
        $ds1->addSeries($load5);

        $warn = new PerfdataSeries('warning', $this->generateFixedData(5));
        $crit = new PerfdataSeries('critical', $this->generateFixedData(7));

        $ds1->addSeries($warn);
        $ds1->addSeries($crit);

        $pdr->addDataset($ds1);

        return $pdr;
    }

    public function generate(PerfdataRequest $req): PerfdataResponse
    {
        $now = new DateTimeImmutable();

        $int = new DateInterval('PT12H');

        $perfdataresponse = new PerfdataResponse();

        try {
            $int = new DateInterval($req->getDuration());
        } catch (Exception $e) {
            $perfdataresponse->addError(sprintf('Failed to parse date interval: %s', $e->getMessage()));
            return $perfdataresponse;
        }

        $diff = $now->sub($int);

        $seconds = $now->getTimestamp() - $diff->getTimestamp();
        $c = $seconds / 600;

        if ($c > 1200) {
            $c = 1000;
        }

        $this->count = $c;

        $serviceName = $req->getServicename();

        if ($serviceName === 'load') {
            $perfdataresponse = $this->generateLoadData();
        } elseif ($serviceName === 'hostalive') {
            $perfdataresponse = $this->generatePingData();
        } elseif ($serviceName === 'ping4') {
            $perfdataresponse = $this->generatePingData();
        } elseif ($serviceName === 'ping6') {
            $perfdataresponse = $this->generatePingData();
        } elseif ($serviceName === 'disk') {
            $perfdataresponse = $this->generateDiskData();
        } elseif ($serviceName === 'swap') {
            $perfdataresponse = $this->generateMuchData();
        } else {
            $perfdataresponse = $this->generateOtherData();
        }

        return $perfdataresponse;
    }
}
