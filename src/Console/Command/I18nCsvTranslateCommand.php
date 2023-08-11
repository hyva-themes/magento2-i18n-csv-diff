<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2021-present. All rights reserved.
 * This product is licensed under the terms of the BSD-3-Clause license.
 * See the LICENSE.txt fi,e for more information.
 */

declare(strict_types=1);

namespace Hyva\I18nCsvDiff\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ClientFactory as HttpClientFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
 * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
 */
class I18nCsvTranslateCommand extends Command
{
    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(HttpClientFactory $httpClientFactory, ScopeConfigInterface $scopeConfig)
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->scopeConfig       = $scopeConfig;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('i18n:translate-csv');
        $this->setDescription(
            'Translate a Magento2 localization CSV to a language using DeepL. ' .
            'Be sure to check the translation afterwards!'
        );
        $this->addOption('in', null, InputOption::VALUE_OPTIONAL, 'Input CSV', 'stdin');
        $this->addOption('out', null, InputOption::VALUE_OPTIONAL, 'Input CSV', 'stdout');
        $this->addArgument('target-lang', InputArgument::REQUIRED, 'The target language code (for example NL or DE)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inFileHandle  = $input->getOption('in') === 'stdin'
            ? STDIN
            : fopen($input->getOption('in'), 'r');
        $outFileHandle = $input->getOption('out') === 'stdout'
            ? STDOUT
            : fopen($input->getOption('out'), 'r');

        $lang = strtoupper($input->getArgument('target-lang'));

        while (!feof($inFileHandle)) {
            $row = fgetcsv($inFileHandle);

            // ignore empty lines and known invalid record
            if (!$row || $row[0] === null) {
                continue;
            }
            fputcsv($outFileHandle, [$row[0], $this->translate($lang, $row[0])]);
        }

        return 0;
    }

    private function translate(string $lang, string $phrase): string
    {
        $key = $this->scopeConfig->getValue('hyva_themes_i18n/translation/deepl_api_key');

        $client = $this->httpClientFactory->create();
        $client->post('https://api-free.deepl.com/v2/translate', [
            'tag_handling'    => 'xml',
            'split_sentences' => 'nonewlines',
            'source_lang'     => 'EN',
            'auth_key'        => $key,
            'text'            => $phrase,
            'target_lang'     => $lang,
        ]);
        $result = json_decode($client->getBody(), true);
        return $result['translations'][0]['text'] ?? $phrase;
    }

}
