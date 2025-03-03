<?php

namespace ApacheSolrForTypo3\Solr\Tests\Integration\FrontendEnvironment;

use ApacheSolrForTypo3\Solr\FrontendEnvironment\Tsfe;
use ApacheSolrForTypo3\Solr\Tests\Integration\IntegrationTest;
use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TsfeTest extends IntegrationTest
{
    /**
     * @test
     */
    public function initializeTsfeWithNoDefaultPageAndPageErrorHandlerDoNotThrowAnError()
    {
        self::markTestSkipped('Since TSFE is isolated/capsuled, no exceptions are thrown or delegated to else where.
        Other scenario is wanted for:
        https://github.com/TYPO3-Solr/ext-solr/issues/2914
        https://github.com/TYPO3-Solr/ext-solr/pull/2915/files');
        $this->expectException(RuntimeException::class);
        $this->importCSVDataSet(__DIR__ . '/Fixtures/initialize_tsfe_with_no_default_page_and_page_error_handler_do_not_throw_an_error.csv');

        $defaultLanguage = $this->buildDefaultLanguageConfiguration('EN', '/en/');
        $defaultLanguage['solr_core_read'] = 'core_en';

        $this->writeSiteConfiguration(
            'integration_tree_one',
            $this->buildSiteConfiguration(1, 'http://testone.site/'),
            [
                $defaultLanguage,
            ],
            $this->buildErrorHandlingConfiguration('Page', [404])
        );

        $scheme = 'http';
        $host = 'localhost';
        $port = 8999;
        $globalSolrSettings = [
            'solr_scheme_read' => $scheme,
            'solr_host_read' => $host,
            'solr_port_read' => $port,
            'solr_timeout_read' => 20,
            'solr_path_read' => '/solr/',
            'solr_use_write_connection' => false,
        ];
        $this->mergeSiteConfiguration('integration_tree_one', $globalSolrSettings);
        clearstatcache();
        usleep(500);
        $siteCreatedHash = md5($scheme . $host . $port . '0-PageErrorHandler');
        self::$lastSiteCreated = $siteCreatedHash;

        $tsfeManager = GeneralUtility::makeInstance(Tsfe::class);
        $tsfeManager->getTsfeByPageIdAndLanguageId(1);
    }
}
