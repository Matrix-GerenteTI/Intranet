<?php
namespace MehrdadDadkhah\File;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Finder
{
    private $finder = 'find';

    /**
     * set finder
     * 
     * @param string $finder find module
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * get find command
     * 
     * @param  string $path     search directory path
     * @param  string $fileName search name
     * @return string           command
     */
    public function getFindCommand(string $path, string $fileName): string
    {
        switch ($this->finder) {
            case 'locate':
                return "locate '$fileName'";
                break;
            
            default:
                return "find $path -name '$fileName'";
                break;
        }
    }

    /**
     * search and find file
     *
     * @param  string  $file       filename or file related path or regex
     * @param  string  $searchPath base path to search
     * @param  boolean $info       return extra file information or not
     * @return array               search result path
     */
    public function findFile(string $file, string $searchPath = '/home', $info = true): array
    {
        
        $fileInfo = new \SplFileInfo($file);
        var_dump( $fileInfo->getPath()  );
        if (!empty($fileInfo->getPath()) && $this->finder == 'find') {
            $searchPath = $this->findDirectoryPath($fileInfo->getPath(), $searchPath);

            $searchPath = implode(' ', $searchPath);

            $file = $fileInfo->getBaseName();
            
        }

        $command = $this->getFindCommand($searchPath, $file);

        $process = new Process($command);
        $process->run();

        // if (!$process->isSuccessful() && strlen($process->getErrorOutput()) > 2) {
        //     throw new ProcessFailedException($process);
        // }

        $files = $this->listResult($process->getOutput());

        if ($info) {
            return $this->makeData($files);
        }

        return $files;
    }

    /**
     * find directory full path
     *
     * @param  string $path       related directory path
     * @param  string $searchPath base search path
     * @return array              return find path list
     */
    public function findDirectoryPath(string $path, string $searchPath = '/'): array
    {
        $directories = explode('/', $path);

        $directoryName = end($directories);

        $command = $this->getFindCommand($searchPath, $directoryName) . " | grep $path";

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->listResult($process->getOutput());
    }

    /**
     * convert console output string to array
     *
     * @param  string $output output of command result
     * @return array         list of path
     */
    private function listResult(string $output): array
    {
        $dataArray = explode("\n", $output);

        if (empty(end($dataArray))) {
            unset($dataArray[count($dataArray) - 1]);
        }

        return $dataArray;
    }

    /**
     * fetch full data of files
     *
     * @param  array  $files list of file path
     * @return array        files data
     */
    public function makeData(array $files): array
    {
        $data = [];

        foreach ($files as $filePath) {
            $fileInfo = new \SplFileInfo($filePath);

            $data[] = [
                'path'       => $fileInfo->getPath(),
                'filename'   => $fileInfo->getFilename(),
                'realpath'   => $fileInfo->getRealpath(),
                'extension'  => $fileInfo->getExtension(),
                'type'       => $fileInfo->getType(),
                'mime_type'  => mime_content_type($filePath),
                'size'       => $fileInfo->getSize(),
                'isFile'     => $fileInfo->isFile(),
                'isDir'      => $fileInfo->isDir(),
                'isLink'     => $fileInfo->isLink(),
                'writable'   => $fileInfo->isWritable(),
                'readable'   => $fileInfo->isReadable(),
                'executable' => $fileInfo->isExecutable(),
            ];
        }

        return $data;
    }
}
