<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Plugin\ReCaptcha;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\ValidationConfigResolver;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\Store\Model\ScopeInterface;
use RJDS\DisableCaptcha\Model\ReCaptcha\NullValidationConfig;

class SafeValidationConfigResolver
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var NullValidationConfig
     */
    private $nullValidationConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        NullValidationConfig $nullValidationConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->nullValidationConfig = $nullValidationConfig;
    }

    /**
     * Avoid hard crashes when a captcha form has an empty/invalid type.
     *
     * @param ValidationConfigResolver $subject
     * @param callable $proceed
     * @param string $key
     * @return ValidationConfigInterface
     * @throws InputException
     */
    public function aroundGet(ValidationConfigResolver $subject, callable $proceed, string $key): ValidationConfigInterface
    {
        try {
            return $proceed($key);
        } catch (InputException $exception) {
            if ($this->isDisabledCaptchaType($key)) {
                return $this->nullValidationConfig;
            }

            throw $exception;
        }
    }

    private function isDisabledCaptchaType(string $key): bool
    {
        $frontendType = (string)$this->scopeConfig->getValue(
            'recaptcha_frontend/type_for/' . $key,
            ScopeInterface::SCOPE_WEBSITE
        );
        $backendType = (string)$this->scopeConfig->getValue('recaptcha_backend/type_for/' . $key);

        return in_array($frontendType, ['', '0'], true) || in_array($backendType, ['', '0'], true);
    }
}
