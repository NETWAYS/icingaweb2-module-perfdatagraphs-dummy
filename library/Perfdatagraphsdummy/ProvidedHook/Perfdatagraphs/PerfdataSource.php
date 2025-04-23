<?php

namespace Icinga\Module\Perfdatagraphsdummy\ProvidedHook\PerfdataGraphs;

use Icinga\Module\Perfdatagraphsdummy\Generator\Dummy;
use Icinga\Module\Perfdatagraphsdummy\Generator\Error;

use Icinga\Module\Perfdatagraphs\Hook\PerfdataSourceHook;
use Icinga\Module\Perfdatagraphs\Model\PerfdataRequest;
use Icinga\Module\Perfdatagraphs\Model\PerfdataResponse;

use Icinga\Application\Config;
use Icinga\Application\Logger;

class PerfdataSource extends PerfdataSourceHook
{
    public function getName(): string
    {
        return 'Dummy';
    }

    public function fetchData(PerfdataRequest $req): PerfdataResponse
    {
        $perfdataresponse = new PerfdataResponse();

        $errorMode = false;

        try {
            $moduleConfig = Config::module('perfdatagraphsdummy');
            $errorMode = (bool) $moduleConfig->get('dummy', 'error_mode', false);
        } catch (Exception $e) {
            Logger::error('Failed to load Perfdata Graphs Dummy module configuration: %s', $e);
        }

        if ($errorMode) {
            $g = new Error();
            $perfdataresponse = $g->generate($req);
        } else {
            $g = new Dummy();
            $perfdataresponse = $g->generate($req);
        }

        return $perfdataresponse;
    }
}
