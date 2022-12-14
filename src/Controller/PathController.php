<?php

namespace Iwmedien\Htaccescheck\Controller;

use Iwmedien\Htaccescheck\Utilities\RemoveDuplicates;

class PathController extends ActionController
{

    public function __construct(public string $directory = '', public string $varFolder = '')
    {
        $this->varFolder = dirname(__DIR__, 4) . '/var/';
        $this->directory = dirname(__DIR__, 4) . '/var/iwmedien/';
        parent::__construct();
    }


    /**
     * @var string
     */
    final public const FILE = 'paths.csv';

    /**
     * @var string
     */
    final public const SAVE = 'save';

    /**
     * @var string
     */
    final public const FAIL = 'fail';

    /**
     * @var string
     */
    final const DUPLICATE = 'duplicate';

    /**
     * @var array<string, string>
     */
    final public const MESSAGE = [
        self::SAVE => '<span class="text-success">url gespeichert</span>',
        self::FAIL => '<span class="text-error">url nicht korrekt</span>',
        self::DUPLICATE => '<span class="bd-grey">url bereits vorhanden</span>'
    ];




    public function addPath($arguments): void
    {
        self::configureFolder();
        $response = self::checkUrl($arguments);
        if ($response === 'save') {
            $fp = fopen($this->directory . self::FILE, 'a+');
            fputcsv($fp, $arguments, ';');
            fclose($fp);
        }

        echo '<div class="container"><div class="row">' . self::MESSAGE[$response] . '</div></div>';
    }

    /**
     * @return mixed[]
     */
    public function getPaths(): array
    {
        self::configureFolder();
        $paths = [];
        $fp = fopen($this->directory . self::FILE, 'r');
        while (($line = fgetcsv($fp)) !== false) {
            $paths[] = $line[0];
        }

        return $paths ?? [];
    }


    public function removePath($arguments): void
    {
    }

    private function configureFolder(): void
    {

        if (!is_dir($this->varFolder)) {
            mkdir($this->varFolder);
        }
        
        if (!is_dir($this->directory)) {
            mkdir($this->directory);
        }

        if (!file_exists($this->directory . self::FILE)) {
            ($pathsFile = fopen($this->directory . self::FILE, "w+")) || die("Unable to open file!");
            fclose($pathsFile);
        }
    }

    private function urlExists(string $path): bool
    {
        $paths = $this->getPaths();
        return in_array($path, $paths);
    }


    private function checkUrl($arguments): string
    {
        $value = self::SAVE;
        if (!filter_var($arguments['path'], FILTER_VALIDATE_URL)) {
            $value = self::FAIL;
        }

        if ($this->urlExists($arguments['path'])) {
            return self::DUPLICATE;
        }

        return $value;
    }

    public function getPathToCSVFile()
    {
        return $this->directory . self::FILE;
    }
}