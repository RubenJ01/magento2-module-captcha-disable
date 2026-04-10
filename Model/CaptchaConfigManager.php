<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Model;

use Magento\Framework\App\Config\Storage\WriterInterface;

class CaptchaConfigManager
{
    /**
     * @var array<string, string>
     */
    private const DISABLED_CONFIG = [
        'customer/captcha/enable' => '0',
        'customer/captcha/forms' => '',
        'customer/captcha/mode' => 'after_fail',
        'admin/captcha/enable' => '0',
        'admin/captcha/forms' => '',
        'admin/captcha/mode' => 'after_fail',
        'msp_securitysuite_recaptcha/frontend/enabled' => '0',
        'msp_securitysuite_recaptcha/backend/enabled' => '0',
    ];

    /**
     * @var string[]
     */
    private const RECAPTCHA_TYPE_PATHS = [
        'recaptcha_frontend/type_for/customer_login',
        'recaptcha_frontend/type_for/customer_create',
        'recaptcha_frontend/type_for/customer_forgot_password',
        'recaptcha_frontend/type_for/customer_edit',
        'recaptcha_frontend/type_for/contact',
        'recaptcha_frontend/type_for/newsletter',
        'recaptcha_frontend/type_for/product_review',
        'recaptcha_frontend/type_for/sendfriend',
        'recaptcha_frontend/type_for/wishlist',
        'recaptcha_frontend/type_for/coupon_code',
        'recaptcha_frontend/type_for/place_order',
        'recaptcha_frontend/type_for/paypal_payflowpro',
        'recaptcha_frontend/type_for/braintree',
        'recaptcha_backend/type_for/user_login',
        'recaptcha_backend/type_for/user_forgot_password',
    ];

    /**
     * @var array<string, string>
     */
    private const ENABLED_CONFIG = [
        'customer/captcha/enable' => '1',
        'customer/captcha/mode' => 'after_fail',
        'admin/captcha/enable' => '1',
        'admin/captcha/mode' => 'after_fail',
        'msp_securitysuite_recaptcha/frontend/enabled' => '1',
        'msp_securitysuite_recaptcha/backend/enabled' => '1',
        'recaptcha_frontend/type_for/customer_login' => 'recaptcha',
        'recaptcha_frontend/type_for/customer_create' => 'recaptcha',
        'recaptcha_frontend/type_for/customer_forgot_password' => 'recaptcha',
        'recaptcha_frontend/type_for/customer_edit' => 'recaptcha',
        'recaptcha_frontend/type_for/contact' => 'recaptcha',
        'recaptcha_frontend/type_for/newsletter' => 'recaptcha',
        'recaptcha_frontend/type_for/product_review' => 'recaptcha',
        'recaptcha_frontend/type_for/sendfriend' => 'recaptcha',
        'recaptcha_frontend/type_for/wishlist' => 'recaptcha',
        'recaptcha_frontend/type_for/coupon_code' => 'recaptcha',
        'recaptcha_frontend/type_for/place_order' => 'recaptcha',
        'recaptcha_frontend/type_for/paypal_payflowpro' => 'recaptcha',
        'recaptcha_frontend/type_for/braintree' => 'recaptcha',
        'recaptcha_backend/type_for/user_login' => 'recaptcha',
        'recaptcha_backend/type_for/user_forgot_password' => 'recaptcha',
    ];

    public function __construct(
        private WriterInterface $configWriter
    ) {
    }

    public function disable(): void
    {
        $this->saveConfig(self::DISABLED_CONFIG);
        $this->deleteConfig(self::RECAPTCHA_TYPE_PATHS);
    }

    public function enable(): void
    {
        $this->saveConfig(self::ENABLED_CONFIG);
    }

    /**
     * @param array<string, string> $config
     */
    private function saveConfig(array $config): void
    {
        foreach ($config as $path => $value) {
            $this->configWriter->save($path, $value);
        }
    }

    /**
     * @param string[] $paths
     */
    private function deleteConfig(array $paths): void
    {
        foreach ($paths as $path) {
            $this->configWriter->delete($path);
        }
    }
}
