<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Plugin\ReCaptcha;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\UiConfigResolver;
use Magento\Store\Model\ScopeInterface;

class SafeUiConfigResolver
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Avoid hard crashes when a captcha form has an empty/invalid type.
     *
     * @param UiConfigResolver $subject
     * @param callable $proceed
     * @param string $key
     * @return array<string, mixed>
     * @throws InputException
     */
    public function aroundGet(UiConfigResolver $subject, callable $proceed, string $key): array
    {
        try {
            return $proceed($key);
        } catch (InputException $exception) {
            if ($this->isDisabledCaptchaType($key)) {
                return [];
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
