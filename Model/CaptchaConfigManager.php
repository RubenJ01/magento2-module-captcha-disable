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
        'recaptcha_frontend/type_for/customer_login' => '0',
        'recaptcha_frontend/type_for/customer_create' => '0',
        'recaptcha_frontend/type_for/customer_forgot_password' => '0',
        'recaptcha_frontend/type_for/customer_edit' => '0',
        'recaptcha_frontend/type_for/contact' => '0',
        'recaptcha_frontend/type_for/newsletter' => '0',
        'recaptcha_frontend/type_for/product_review' => '0',
        'recaptcha_frontend/type_for/sendfriend' => '0',
        'recaptcha_frontend/type_for/wishlist' => '0',
        'recaptcha_frontend/type_for/coupon_code' => '0',
        'recaptcha_frontend/type_for/place_order' => '0',
        'recaptcha_frontend/type_for/paypal_payflowpro' => '0',
        'recaptcha_frontend/type_for/braintree' => '0',
        'recaptcha_backend/type_for/user_login' => '0',
        'recaptcha_backend/type_for/user_forgot_password' => '0',
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
        'recaptcha_backend/type_for/user_login' => 'recaptcha',
        'recaptcha_backend/type_for/user_forgot_password' => 'recaptcha',
    ];

    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(WriterInterface $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    public function disable(): void
    {
        $this->saveConfig(self::DISABLED_CONFIG);
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
}
