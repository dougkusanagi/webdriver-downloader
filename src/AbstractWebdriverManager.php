<?php

namespace Src;

abstract class AbstractWebdriverManager
{
    abstract public function getDownloadUrl(string $version): string;
}
