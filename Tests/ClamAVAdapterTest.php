<?php

namespace CL\Tissue\Adapter\ClamAV\Tests;

use CL\Tissue\Adapter\ClamAV\ClamAVAdapter;
use CL\Tissue\Tests\Adapter\AdapterTestCase;

class ClamAVAdapterTest extends AdapterTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (!$clamScanPath = $this->findExecutable('clamscan', 'CLAMSCAN_BIN')) {
            $this->markTestSkipped('Unable to locate `clamscan` executable.');
        }

        return new ClamAVAdapter($clamScanPath);
    }

    /**
     * @expectedException \CL\Tissue\Exception\AdapterException
     * @expectedExceptionMessage The `clamscan` or `clamdscan` executable could not be found
     */
    public function testInvalidBinary()
    {
        new ClamAVAdapter('/path/to/non-existing/binary');
    }
}
