# Webdriver Manager

Usefull if you want to download the new version of webdriver. For now, only works for Chrome Webdriver.

## Installation

```bash
composer require dougkusanagi/webdriver-downloader
```

## Usage

```php
require_once '../vendor/autoload.php';

use Src\Enums\BrowserEnum;
use Src\WebdriverManager;

$manager = new WebdriverManager();
$webdriver_exe_path = $manager->download(
    browser: BrowserEnum::CHROME,
    path: '/path_to_download',
    replaceExisting: true
);

/** Or you can use a helper */
$webdriver_exe_path = $manager->downloadForChrome(
    path: '/path_to_download',
    replaceExisting: true
);

/** /path_to_download/chromedriver.exe */
echo $webdriver_exe_path;
```
