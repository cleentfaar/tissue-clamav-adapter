<?php

namespace CL\Tissue\Adapter\ClamAV\Tests;

use CL\Tissue\Adapter\ClamAv\ClamAvAdapter;
use CL\Tissue\Tests\Adapter\AbstractAdapterTestCase;

class ClamAvAdapterTest extends AbstractAdapterTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (!$clamScanPath = $this->findExecutable('clamscan', 'CLAMSCAN_BIN')) {
            $this->markTestSkipped('Unable to locate `clamscan` executable.');
        }

        return new ClamAvAdapter($clamScanPath);
    }

    /**
     * @expectedException \CL\Tissue\Exception\AdapterException
     * @expectedExceptionMessage The path to `clamscan` or `clamdscan` could not be found or is not executable
     */
    public function testInvalidClamScanPath()
    {
        new ClamAvAdapter('/path/to/non-existing/binary');
    }
}
