<?php

namespace CL\Tissue\Adapter\ClamAV\Tests;

use CL\Tissue\Adapter\ClamAV\ClamAVAdapter;
use CL\Tissue\Tests\Adapter\AdapterTestCase;

/**
 * @group integration
 */
class ClamAVAdapterTest extends AdapterTestCase
{
    /**
     * @var ClamAVAdapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $testFile1;

    /**
     * @var string
     */
    private $testFile2;

    /**
     * @var string
     */
    private $clamScanPath;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!$clamscanPath = $this->findExecutable('clamscan', 'CLAMSCAN_BIN')) {
            $this->markTestSkipped('Unable to locate `clamscan` executable.');
        }

        $this->testFile1 = $this->getPathToTestFile('foobar1.txt');
        $this->testFile2 = $this->getPathToTestFile('foobar2.txt');
        $this->clamScanPath = $clamscanPath;
    }

    /**
     * @expectedException \CL\Tissue\Exception\AdapterException
     * @expectedExceptionMessage The `clamscan` or `clamdscan` executable could not be found
     */
    public function testInvalidClamScanPath()
    {
        new ClamAVAdapter('/path/to/non-existing/binary');
    }

    /**
     * @expectedException \CL\Tissue\Exception\AdapterException
     * @expectedExceptionMessage You must supply either a string or an array of paths to scan
     */
    public function testInvalidScanPath()
    {
        $adapter = new ClamAVAdapter($this->clamScanPath);
        $adapter->scan(new \stdClass());
    }

    public function testScanSingle()
    {
        $adapter = new ClamAVAdapter($this->clamScanPath);
        $result = $adapter->scan($this->testFile1);

        $this->assertCount(1, $result->getFiles());
        $this->assertCount(0, $result->getDetections());
    }

    public function testScanMultiple()
    {
        $adapter = new ClamAVAdapter($this->clamScanPath);
        $result = $adapter->scan([$this->testFile1, $this->testFile2]);

        $this->assertCount(2, $result->getFiles());
        $this->assertCount(0, $result->getDetections());
    }

    public function testScanWithDetection()
    {
        $adapter = new ClamAVAdapter($this->clamScanPath);
        $result = $adapter->scan(__DIR__ . '/Fixtures/virus.txt');

        $this->assertCount(1, $result->getFiles());
        $this->assertCount(1, $result->getDetections());
    }
}
