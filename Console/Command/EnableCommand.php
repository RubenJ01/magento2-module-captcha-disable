<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\Framework\ObjectManagerInterface;
use RJDS\DisableCaptcha\Model\CaptchaConfigManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class EnableCommand extends Command
{
    public function __construct(
        private CaptchaConfigManager $captchaConfigManager,
        private CacheManager $cacheManager,
        private State $appState,
        private ObjectManagerInterface $objectManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('rjds:captcha:enable');
        $this->setDescription('Enable captcha and reCAPTCHA settings with default invisible mode');
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Skip production mode confirmation prompt'
        );
        $this->addOption(
            'enable-2fa-module',
            null,
            InputOption::VALUE_NONE,
            'Enable Magento_TwoFactorAuth module again'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->confirmInProduction($input, $output)) {
            $output->writeln('<comment>Aborted.</comment>');
            return Command::SUCCESS;
        }

        $this->captchaConfigManager->enable();
        if ($input->getOption('enable-2fa-module')) {
            $this->enableTwoFactorAuthModule($output);
        }
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        $output->writeln('<info>Captcha and reCAPTCHA settings have been enabled.</info>');
        $output->writeln('<comment>Ensure site keys/domains are configured correctly.</comment>');

        return Command::SUCCESS;
    }

    private function confirmInProduction(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption('force')) {
            return true;
        }

        if ($this->appState->getMode() !== State::MODE_PRODUCTION) {
            return true;
        }

        $helper = $this->getHelper('question');
        if (!$helper) {
            return false;
        }

        $question = new ConfirmationQuestion(
            '<question>Application mode is production. Continue enabling captchas? [y/N]</question> ',
            false
        );

        return (bool)$helper->ask($input, $output, $question);
    }

    private function enableTwoFactorAuthModule(OutputInterface $output): void
    {
        $moduleName = 'Magento_TwoFactorAuth';
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->get(ModuleList::class);
        if ($moduleList->has($moduleName)) {
            $output->writeln('<comment>Magento_TwoFactorAuth is already enabled.</comment>');
            return;
        }

        /** @var ModuleStatus $moduleStatus */
        $moduleStatus = $this->objectManager->get(ModuleStatus::class);
        $errors = $moduleStatus->checkConstraints(true, [$moduleName]);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            return;
        }

        $moduleStatus->setIsEnabled(true, [$moduleName]);
        $output->writeln('<info>Magento_TwoFactorAuth module enabled. Run setup:upgrade after this command.</info>');
    }
}
