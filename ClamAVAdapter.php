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
    protected $clamScanPath;

    /**
     * @var string
     */
    protected $databasePath;

    /**
     * @param string      $clamScanPath
     * @param string|null $databasePath
     *
     * @throws AdapterException If the given path to clamscan is not executable
     * @throws \LogicException If you supplied a path to the daemon-executable and a path to the database (incompatible)
     */
    public function __construct($clamScanPath, $databasePath = null)
    {
        if (!is_executable($clamScanPath)) {
            throw new AdapterException(sprintf(
                'The path to `clamscan` or `clamdscan` could not be found or is not executable (path: %s)',
                $clamScanPath
            ));
        }

        if ($databasePath !== null && $this->usesDaemon($clamScanPath)) {
            throw new \LogicException('You can\'t supply a database-path if you are using the ClamAV daemon service');
        }

        $this->clamScanPath = $clamScanPath;
        $this->databasePath = $databasePath;
    }

    /**
     * {@inheritdoc}
     */
    public function scan(array $paths, array $options = [])
    {
        $files = [];
        $detections = [];
        foreach ($paths as $path) {
            if (!is_string($path)) {
                throw new AdapterException(sprintf(
                    'You must supply an array of strings (paths) to scan: path with type "%s" given',
                    gettype($path)
                ));
            }

            $process    = $this->createProcess($path, $options);
            $returnCode = $process->run();
            $output     = trim($process->getOutput());
            if (0 !== $returnCode && !strstr($output, ' FOUND')) {
                throw AdapterException::fromProcess($process);
            }

            $result = $this->createScanResult($path, $output);
            $files = array_merge($files, $result->getFiles());
            $detections = array_merge($detections, $result->getDetections());
        }

        return new ScanResult($paths, $files, $detections);
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

        if ($this->usesDaemon($this->clamScanPath)) {
            // Pass filedescriptor to clamd (useful if clamd is running as a different user)
            $pb->add('--fdpass');
        } elseif ($this->databasePath !== null) {
            // Only the (isolated) binary version can change the signature-database used
            $pb->add(sprintf('--database=%s', $this->databasePath));
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
    private function usesDaemon($path)
    {
        return substr($path, -9) === 'clamdscan';
    }
}
