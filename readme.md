# Webdriver Manager

Usefull if you want to download the new version of webdriver. For now, only works for Chrome Webdriver.

## Installation

```bash
composer require dougkusanagi/webdriver-downloader
```

## Usage

```php
$webdriver_exe_path = (new WebdriverManager)
    ->download(
        browser: BrowserEnum::CHROME,
        path: '/path_to_download',
        force: true
    );

var_dump($webdriver_exe_path);

/** Or you can use a helper */

$webdriver_exe_path = (new WebdriverManager)
    ->downloadChromeWebdriver(
        path: '/path_to_download',
        force: true
    );

var_dump($webdriver_exe_path);
```
