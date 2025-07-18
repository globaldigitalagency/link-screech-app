# Link Screech Project

## Setup

### Screaming Frog SEO Spider

- In the directory ``.ScreamingFrogSEOSpider`` in the project root
  - Create a file named  ``licence.txt`` with the licence key and username
```
myusername
XXXX-XXXX-XXXX-XXXX
```

## Usage

- Run the script with the command:
```
php bin/console screamingfrog:run <input-file> <crawl-name>
```

### Screaming Frog SEO Spider CLI

```
screamingfrogseospider 
  --crawl <url> 
  --headless 
  --output-folder /var/www/.ScreamingFrogSEOSpider/reports/<crawl-name>
  --config /var/www/.ScreamingFrogSEOSpider/config/config.seospiderconfig 
  --export-format csv 
  --bulk-export All Outlinks 
  --export-tabs Response Codes:External No Response,URL:All 
  --timestamped-output 
  --save-report Crawl Overview
```

## Dependencies

- Screaming Frog SEO Spider CLI
