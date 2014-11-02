<?php

namespace CL\Tissue\Adapter\ClamAV;

use CL\Tissue\Adapter\AbstractAdapter;
use CL\Tissue\Exception\AdapterException;
use CL\Tissue\Model\Detection;
use CL\Tissue\Model\ScanResult;

class ClamAVAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    private $clamScanPath;

    /**
     * @param string $clamScanPath
     */
    public function __construct($clamScanPath)
    {
        if (!is_executable($clamScanPath)) {
            throw new AdapterException(sprintf(
                'The `clamscan` or `clamdscan` executable could not be found in: "%s"',
                $clamScanPath
            ));
        }

        $this->clamScanPath = $clamScanPath;
    }

    /**
     * {@inheritdoc}
     */
    public function scan($path, array $options = [])
    {
        if (is_array($path)) {
            // clamscan does not seem to support scanning of multiple targets at the same time...
            return $this->scanArray($path, $options);
        } elseif (!is_string($path)) {
            throw new AdapterException(sprintf(
                'You must supply either a string or an array of paths to scan: "%s" given',
                gettype($path)
            ));
        }

        $process = $this->createProcess($path, $options);
        $returnCode = $process->run();
        $output = trim($process->getOutput());
        if (0 !== $returnCode && !strstr($output, ' FOUND')) {
            throw AdapterException::fromProcess($process);
        }

        return $this->createScanResult($path, $output);
    }

    /**
     * @param string $path
     * @param array  $options
     *
     * @return \Symfony\Component\Process\Process
     */
    private function createProcess($path, array $options)
    {
        $pb = $this->createProcessBuilder([$this->clamScanPath]);
        $pb->add('--no-summary');

        if ($this->usesDaemon()) {
            // needed to bypass errors when executed under a different user
            // this probably applies to almost every application
            $pb->add('--fdpass');
        }

        $pb->add($path);

        return $pb->getProcess();
    }

    /**
     * @param string $path
     * @param string $output
     *
     * @return ScanResult
     */
    private function createScanResult($path, $output)
    {
        $lines = explode("\n", $output);
        $files = [];
        $detections = [];
        foreach ($lines as $line) {
            $file = substr($line, 0, strripos($line, ':'));
            if (substr($line, -3) !== ' OK') {
                $afterFile = substr($line, strripos($line, ':') + 1);
                $description = substr($afterFile, 0, -7);
                $detections[] = $this->createDetection($file, Detection::TYPE_VIRUS, $description);
            }
            $files[] = $file;
        }

        return new ScanResult($path, $files, $detections);
    }

    /**
     * @return bool
     */
    private function usesDaemon()
    {
        return substr($this->clamScanPath, -9) === 'clamdscan';
    }
}
