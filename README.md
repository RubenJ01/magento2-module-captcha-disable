# RJDS Disable Captcha

A small Magento 2 module to toggle all captcha and reCAPTCHA settings with CLI commands.

## Installation

Enable the module and run setup:

```bash
bin/magento module:enable RJDS_DisableCaptcha
bin/magento setup:upgrade
bin/magento cache:flush
```

## Usage

Disable all captcha/reCAPTCHA settings:

```bash
bin/magento rjds:captcha:disable
```

Enable captcha/reCAPTCHA settings again (using default invisible mode):

```bash
bin/magento rjds:captcha:enable
```

## Production safety

In production mode, both commands ask for confirmation first.

Use `--force` to skip the confirmation prompt:

```bash
bin/magento rjds:captcha:disable --force
bin/magento rjds:captcha:enable --force
```

## Notes

- Installing this module does **not** change captcha configuration by itself.
- Configuration is only changed when running the CLI commands.
