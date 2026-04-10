<?php
declare(strict_types=1);

namespace RJDS\DisableCaptcha\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\App\Cache\Manager as CacheManager;
use RJDS\DisableCaptcha\Model\CaptchaConfigManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class EnableCommand extends Command
{
    /**
     * @var CaptchaConfigManager
     */
    private $captchaConfigManager;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        CaptchaConfigManager $captchaConfigManager,
        CacheManager $cacheManager,
        State $appState
    ) {
        parent::__construct();
        $this->captchaConfigManager = $captchaConfigManager;
        $this->cacheManager = $cacheManager;
        $this->appState = $appState;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->confirmInProduction($input, $output)) {
            $output->writeln('<comment>Aborted.</comment>');
            return Command::SUCCESS;
        }

        $this->captchaConfigManager->enable();
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
}
