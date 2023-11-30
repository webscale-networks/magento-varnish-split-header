# Webscale_SplitHeader module
Sub-module responsible for split X-Magento-Tags header when varnish selected as a cache solution.

## Installation
To install extension run the following in magento root directory:

```console
composer config repositories.webscale-networks-split-header git https://github.com/webscale-networks/magento-varnish-split-header.git
```

To avoid issues with CI/CD and github add `"no-api": true` to the repo settings, so it looks like this:
```console
"webscale-networks": {
    "type": "git",
    "url": "https://github.com/webscale-networks/magento-varnish-split-header.git",
    "no-api": true
}
```

Now require extension itself:
```console
composer require webscale-networks/magento-varnish-split-header
```

After composer installs the package run next Magento commands:

```console
php bin/magento module:enable Webscale_SplitHeader
php bin/magento setup:upgrade
bin/magento cache:clean
```

Once completed log in to the Magento admin panel and proceed to configuring the extension.

## Configuration

Open a browser, log in to the Magento admin and navigate to:
```
Stores > Configuration > Webscale > Varnish
```

Enable the module by switching `Enabled` to `Yes` under `Enable Split Tags` and save the configuration.
