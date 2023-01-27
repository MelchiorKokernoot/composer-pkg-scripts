<?php declare(strict_types=1);

namespace MelchiorKokernoot\ComposerPkgScripts\Command;

use Composer\Command\BaseCommand;
use MelchiorKokernoot\ComposerPkgScripts\Script\ScriptManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpPackageScriptsCommand extends BaseCommand
{
    /** @var ScriptManager */
    private $scriptManager;

    public function __construct(ScriptManager $scriptManager)
    {
        parent::__construct();

        $this->scriptManager = $scriptManager;
    }

    protected function configure(): void
    {
        $this->setName('package-scripts:dump');
        $this->setAliases(['psd']);
        $this->addOption('vars', null, InputOption::VALUE_NONE, 'Dump script variables');
        $this->setDescription('Dump compiled scripts (including root package scripts)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('vars')) {
            $value = $this->scriptManager->getPackageVariables();
        } else {
            $value = $this->scriptManager->getCompiledScripts();
        }

        $this->dump($value, $output);

        return 0;
    }

    private function dump($value, OutputInterface $output, int $level = 0): void
    {
        if (is_array($value)) {
            if (empty($value)) {
                $output->write('<comment>[]</comment>');
            }

            if ($level > 0) {
                $output->write("\n");
            }

            foreach ($value as $k => $v) {
                if ($level > 0) {
                    $output->write(str_repeat('    ', $level));
                }

                $output->write(sprintf('<info>%s:</info> ', $k));

                $this->dump($v, $output, $level + 1);
            }

            return;
        }

        $output->writeln(sprintf('<comment>%s</comment>', $value));
    }
}
