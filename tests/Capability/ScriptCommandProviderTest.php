<?php declare(strict_types=1);

namespace MelchiorKokernoot\ComposerPkgScripts\Capability;

use Composer\Command\ScriptAliasCommand;
use MelchiorKokernoot\ComposerPkgScripts\Command\DumpPackageScriptsCommand;
use MelchiorKokernoot\ComposerPkgScripts\Command\ListPackageScriptsCommand;
use MelchiorKokernoot\ComposerPkgScripts\Plugin;
use MelchiorKokernoot\ComposerPkgScripts\Script\Script;
use MelchiorKokernoot\ComposerPkgScripts\Script\ScriptManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class ScriptCommandProviderTest extends TestCase
{
    function testShouldGetCommands()
    {
        $scriptManagerMock = $this->createConfiguredMock(ScriptManager::class, [
            'getRegisteredScripts' => [
                'acme:example:foo' => $fooScript = new Script('acme/example', 'foo', 'acme:example:foo', ['foo'], 'acme foo', 'foo help'),
                'foo' => $fooScript,
                'acme:example:baz' => new Script('acme/example', 'baz', 'acme:example:baz', [], 'acme baz', 'baz help'),
            ],
        ]);

        $pluginMock = $this->createConfiguredMock(Plugin::class, [
            'getScriptManager' => $scriptManagerMock,
        ]);

        $commandProvider = new ScriptCommandProvider(['plugin' => $pluginMock]);

        /** @var Command[] $commands */
        $commands = $commandProvider->getCommands();

        $this->assertCount(5, $commands);
        $this->assertInstanceOf(ListPackageScriptsCommand::class, $commands[0]);
        $this->assertInstanceOf(DumpPackageScriptsCommand::class, $commands[1]);
        $this->assertInstanceOf(ScriptAliasCommand::class, $commands[2]);
        $this->assertInstanceOf(ScriptAliasCommand::class, $commands[3]);
        $this->assertInstanceOf(ScriptAliasCommand::class, $commands[4]);

        $this->assertSame('acme:example:foo', $commands[2]->getName());
        $this->assertSame('foo help', $commands[2]->getDescription());
        $this->assertStringContainsString('"foo"', $commands[2]->getHelp());

        $this->assertSame('foo', $commands[3]->getName());
        $this->assertSame('foo help', $commands[3]->getDescription());
        $this->assertStringContainsString('"foo"', $commands[3]->getHelp());

        $this->assertSame('acme:example:baz', $commands[4]->getName());
        $this->assertSame('baz help', $commands[4]->getDescription());
        $this->assertStringContainsString('"baz"', $commands[4]->getHelp());
    }
}
