<?php

namespace App\Enum;

enum CrawlFileEnum: string
{
    case MAIN_FILE_NAME = 'crawl_overview.csv';
    case ALL_URL_FILE_NAME = 'url_all.csv';
    case EXTERNAL_NO_RESPONSE_FILE_NAME = 'response_codes_external_no_response.csv';
    case OUTLINKS_FILE_NAME = 'all_outlinks.csv';
}
