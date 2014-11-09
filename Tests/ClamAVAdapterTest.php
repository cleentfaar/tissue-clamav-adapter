<?php

namespace CL\Tissue\Adapter\ClamAV\Tests;

use CL\Tissue\Adapter\ClamAV\ClamAVAdapter;
use CL\Tissue\Tests\Adapter\AdapterTestCase;

class ClamAVAdapterTest extends AdapterTestCase
{
    /**
     * @var ClamAVAdapter
     */
    protected $adapter;

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
     * @expectedExceptionMessage The `clamscan` or `clamdscan` executable could not be found
     */
    public function testInvalidClamScanPath()
    {
        new ClamAVAdapter('/path/to/non-existing/binary');
    }
}
