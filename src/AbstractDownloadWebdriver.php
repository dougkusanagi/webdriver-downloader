<?php

namespace Src;

abstract class AbstractDownloadWebdriver
{
    abstract public function handle(string $version, string $path, bool $replaceExisting): string;
}
