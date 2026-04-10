<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Model\ReCaptcha;

use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigExtensionInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;

class NullValidationConfig implements ValidationConfigInterface
{
    public function getPrivateKey(): string
    {
        return '';
    }

    public function getRemoteIp(): string
    {
        return '';
    }

    public function getValidationFailureMessage(): string
    {
        return '';
    }

    public function getExtensionAttributes(): ?ValidationConfigExtensionInterface
    {
        return null;
    }
}
