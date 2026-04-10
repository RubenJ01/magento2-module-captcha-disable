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

class DisableCommand extends Command
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
        $this->setName('rjds:captcha:disable');
        $this->setDescription('Disable all Magento captcha and reCAPTCHA settings');
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Skip production mode confirmation prompt'
        );
        $this->addOption(
            'disable-2fa-module',
            null,
            InputOption::VALUE_NONE,
            'Disable Magento_TwoFactorAuth module to avoid TFA redirects'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->confirmInProduction($input, $output)) {
            $output->writeln('<comment>Aborted.</comment>');
            return Command::SUCCESS;
        }

        $this->captchaConfigManager->disable();
        if ($input->getOption('disable-2fa-module')) {
            $this->disableTwoFactorAuthModule($output);
        }
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        $output->writeln('<info>All captcha and reCAPTCHA settings have been disabled.</info>');

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
            '<question>Application mode is production. Continue disabling captchas? [y/N]</question> ',
            false
        );

        return (bool)$helper->ask($input, $output, $question);
    }

    private function disableTwoFactorAuthModule(OutputInterface $output): void
    {
        $moduleName = 'Magento_TwoFactorAuth';
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->get(ModuleList::class);
        if (!$moduleList->has($moduleName)) {
            $output->writeln('<comment>Magento_TwoFactorAuth is already disabled.</comment>');
            return;
        }

        /** @var ModuleStatus $moduleStatus */
        $moduleStatus = $this->objectManager->get(ModuleStatus::class);
        $errors = $moduleStatus->checkConstraints(false, [$moduleName]);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln('<error>' . $error . '</error>');
            }
            return;
        }

        $moduleStatus->setIsEnabled(false, [$moduleName]);
        $output->writeln('<info>Magento_TwoFactorAuth module disabled. Run setup:upgrade after this command.</info>');
    }
}
