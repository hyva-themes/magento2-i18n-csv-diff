<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2021-present. All rights reserved.
 * This product is licensed under the terms of the BSD-3-Clause license.
 * See the LICENSE.txt file for more information.
 */

declare(strict_types=1);

namespace Hyva\I18nCsvDiff\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_diff as diff;
use function array_search as search;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
 * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
 */
class I18nCsvDiffCommand extends Command
{
    /**
     * @var string[]
     */
    private $ignoreStrings;

    public function __construct(array $ignoreStrings)
    {
        $this->ignoreStrings = $ignoreStrings;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('i18n:diff-csv');
        $this->setDescription('Display all translations that are present in the first CSV file but not in the second.');
        $this->addArgument('file-a', InputArgument::REQUIRED, 'First i18n CSV file');
        $this->addArgument('file-b', InputArgument::REQUIRED, 'Second i18n CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        setlocale(LC_ALL, "en_US.UTF-8");
        $fileA = $input->getArgument('file-a');
        $fileB = $input->getArgument('file-b');

        $recordsA = $this->readFirstCol($fileA);
        sort($recordsA);

        $recordsB = $this->readFirstCol($fileB);
        sort($recordsB);

        $diff = diff($recordsA, $recordsB);

        foreach ($diff as $r) {
            // display the record twice so the output can be copy&pasted into a translation CSV file
            fputcsv(STDOUT, [$r, $r]);
        }
        return 0;
    }

    private function readFirstCol(string $file): array
    {
        $fileHandle = fopen($file, 'r');
        $records    = [];
        while (!feof($fileHandle)) {
            $row = fgetcsv($fileHandle);

            // ignore empty lines and known invalid record
            if ($row && $row[0] !== null && false === search($row[0], $this->ignoreStrings)) {
                $records[] = $row[0];
            }
        }
        return $records;
    }
}
