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
        if (!$clamscanPath = $this->findExecutable('clamdscan', 'CLAMDSCAN_BIN')) {
            if (!$clamscanPath = $this->findExecutable('clamscan', 'CLAMSCAN_BIN')) {
                $this->markTestSkipped('Unable to locate `clamdscan` or `clamscan` executable.');
            }
        }

        return new ClamAVAdapter($clamscanPath);
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
