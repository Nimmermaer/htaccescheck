<?php

namespace Iwmedien\Htaccescheck;

use Iwmedien\Htaccescheck\Utilities\RemoveDuplicates;

class PathController
{
    /**
     * @var string
     */
    final public const DIRECTORY = '../var/iwmedien/';

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
    final const FAIL = 'fail';
    
    /**
     * @var string
     */
    final const DUPLICATE = 'duplicate';

    /**
     * @var array<string, string>
     */
    final public const MESSAGE = [
        self::SAVE => 'url gespeichert',
        self::FAIL => 'url nicht korrekt',
        self::DUPLICATE => 'url bereits vorhanden'
    ];



    public function addPath($arguments): void
    {
        self::configureFolder();
        if (self::checkUrl($arguments) === 'save') {
            $fp = fopen(self::DIRECTORY . self::FILE, 'a+');
            fputcsv($fp, $arguments, ';');
            fclose($fp);
        }

        echo self::MESSAGE[self::checkUrl($arguments)];
    }

    /**
     * @return mixed[]
     */
    public function getPaths(): array
    {
        self::configureFolder();
        $paths = [];
        $fp = fopen(self::DIRECTORY . self::FILE, 'r');
        while (($line = fgetcsv($fp)) !== false) {
            $paths[] = $line[0];
        }
        
        return $paths ?? [];
    }

    public function removePath(): void
    {

    }

    private static function configureFolder(): void
    {

        if (!is_dir(self::DIRECTORY)) {
            mkdir(self::DIRECTORY);
        }

        if (!file_exists(self::DIRECTORY . self::FILE)) {
            ($pathsFile = fopen(self::DIRECTORY . self::FILE, "w+")) || die("Unable to open file!");
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
            $value = self::DUPLICATE;
        }

        return $value;
    }
}