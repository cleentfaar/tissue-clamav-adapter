<?php

namespace CL\Tissue\Adapter\ClamAV\Tests;

use CL\Tissue\Adapter\ClamAV\ClamAVAdapter;
use CL\Tissue\Tests\Adapter\AbstractAdapterTestCase;

class ClamAVAdapterTest extends AbstractAdapterTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (!$clamScanPath = $this->findExecutable('clamscan', 'CLAMSCAN_BIN')) {
            $this->markTestSkipped('Unable to locate `clamscan` executable.');
        }

        $database = isset($_SERVER['CLAMSCAN_DATABASE']) ? $_SERVER['CLAMSCAN_DATABASE'] : null;

        return new ClamAVAdapter($clamScanPath, $database);
    }

    /**
     * @expectedException \CL\Tissue\Exception\AdapterException
     * @expectedExceptionMessage The path to `clamscan` or `clamdscan` could not be found or is not executable
     */
    public function testInvalidClamScanPath()
    {
        new ClamAVAdapter('/path/to/non-existing/binary');
    }
}
