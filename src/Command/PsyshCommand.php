<?php declare(strict_types=1);

namespace ShopwarePsysh\Command;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Psy\Configuration;
use Psy\Shell;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\Struct;
use ShopwarePsysh\Caster\ShopwareCasterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Finder\Finder;

class PsyshCommand extends Command
{
    protected static $defaultName = 'sw:psysh';
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var iterable
     */
    private $casters;

    /**
     * PsyshCommand constructor.
     * @param  string|null  $name
     * @param  iterable  $casters
     */
    public function __construct(iterable $casters, string $name = null)
    {
        parent::__construct($name ?? self::$defaultName);

        $this->casters = $casters;
    }

    /**
     * @internal
     * @required
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

    protected function configure()
    {
        $this->addArgument('include', InputArgument::IS_ARRAY, 'Include file(s) before starting tinker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->setCatchExceptions(false);

        $config = new Configuration();
        $config->loadConfig($input->getArguments());

        $config->setUpdateCheck('never');
        $config->setPrompt('> ');
        $config->setStartupMessage('
<info>Enter list to see available commands</info>
<info>Enter ls to see list of scoped variables</info>
');

        $config->addMatchers([
            new ShopwareClassesMatcher(),
            new ShopwareServicesMatcher(),
        ]);

        $config->getPresenter()->addCasters(
            $this->getCasters()
        );

        try {
            $this->registerAliases();
        } catch (\Throwable $ex) {
        }

        $shell = new Shell($config);

        $shell->setScopeVariables([
            'application' => $this->getApplication(),
            'container' => $this->container,
            'connection' => $this->container->get(Connection::class),
            'commands' => $this->getCommands(),
            'context' => Context::createDefaultContext(),
            'criteria' => new Criteria(),
            'env' => $_ENV
        ]);

        $shell->addCommands($this->getCommands());
        $shell->setIncludes($input->getArgument('include'));

        return $shell->run();
    }

    protected function getCommands(): array
    {
        $commands = $this->getApplication()->all();

        foreach ($commands as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->getApplication()->getContainer());
            }
        }

        return $commands;
    }

    private function registerAliases(): void
    {
        $projectDir = $this->container->get('kernel')->getProjectDir();
        $platformDir = $projectDir . '/platform';

        $finder = new Finder();
        $finder->files()->name('*.php')->in((file_exists($platformDir) ? $platformDir : $projectDir) .'/src/Core');

        foreach ($finder as $file) {
            $namespace = 'Shopware\\Core\\';

            if ($relativePath = $file->getRelativePath()) {
                $namespace .= strtr($relativePath, '/', '\\') . '\\';
            }

            if (strpos($namespace, 'Test')) {
                continue;
            }

            $baseName = $file->getBasename('.php');

            $class = $namespace . $baseName;

            try {
                class_alias($class, $baseName);
            } catch (\Throwable $e) {
                //
            }
        }
    }

    private function getCasters(): array
    {
        $casters = [];

        foreach ($this->casters as $caster) {
            if (!$caster instanceof ShopwareCasterInterface) {
                continue;
            }

            $casters[Struct::class] = [$caster, 'cast'];
        }

        return $casters;
    }
}
