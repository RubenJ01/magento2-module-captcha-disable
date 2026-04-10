# RJDS Disable Captcha

A small Magento 2 module to toggle all captcha and reCAPTCHA settings with CLI commands.

## Requirements

- PHP `>=8.1`
- Magento Open Source / Adobe Commerce `2.4.4` up to `2.4.8`

## Installation

Install via Composer:

```bash
composer require rjds/magento2-module-captcha-disable
```

Then enable the module and run setup:

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

Disable captcha and also disable Magento 2FA module entirely:

```bash
bin/magento rjds:captcha:disable --disable-2fa-module
```

Enable captcha/reCAPTCHA settings again (using default invisible mode):

```bash
bin/magento rjds:captcha:enable
```

Enable captcha and also re-enable Magento 2FA module:

```bash
bin/magento rjds:captcha:enable --enable-2fa-module
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
