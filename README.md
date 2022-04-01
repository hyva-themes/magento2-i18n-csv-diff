# Hyvä Themes - Magento translation CSV comparison command 

[![Hyvä Themes](https://repository-images.githubusercontent.com/300568807/f00eb480-55b1-11eb-93d2-074c3edd2d07)](https://hyva.io/)

## hyva-themes/magento2-i18n-csv-diff

![Supported Magento Versions][ico-compatibility]

This module adds a `bin/magento i18n:diff-csv` command.  
The command takes two CSV files as arguments.    
It displays all translations that are present in the first CSV file but not in the second.

## Usage:

* Collect all Magento and Hyvä strings:
    ```sh
    bin/magento i18n:collect-phrases vendor/magento/ > magento-strings.csv
    bin/magento i18n:collect-phrases vendor/hyva-themes/magento2-default-theme/ > hyva-strings.csv
    bin/magento i18n:collect-phrases vendor/hyva-themes/magento2-theme-module/ >> hyva-strings.csv
    ```
* Find all Hyvä specific translations that are not part of native Magento:
    ```sh
    bin/magento i18n:diff-csv hyva-strings.csv magento-strings.csv
    ```

* Find all translations that are missing in a hyva translation file:
    ```sh
    bin/magento i18n:diff-csv hyva-strings.csv i18n/de_DE.csv
    ```
* Find all translations that are in a translation file but are not used in Hyvä:
    ```sh
    bin/magento i18n:diff-csv i18n/de_DE.csv hyva-strings.csv
    ```
 
## Installation
  
1. Install via composer
    ```
    composer require hyva-themes/magento2-i18n-csv-diff
    ```
2. Enable module
    ```
    bin/magento setup:upgrade
    ```

## Configuration
  
No configuration needed.

  
## License

The BSD-3-Clause License. Please see [License File](LICENSE.txt) for more information.

[ico-compatibility]: https://img.shields.io/badge/magento-%202.3%20|%202.4-brightgreen.svg?logo=magento&longCache=true&style=flat-square
