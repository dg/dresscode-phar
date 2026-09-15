<?php declare(strict_types=1);

namespace DressCode;


/**
 * Serves the classes of the phar to code outside of it, so that a plugin can be written and tested against
 * the API of DressCode with nothing but this package installed. Only the namespaces the API is made of are
 * served, plus the prefixed libraries the served classes need; nothing is registered globally, so the phar
 * never shadows a class the project has of its own.
 */
final class PharAutoloader
{
	private const Phar = 'phar://' . __DIR__ . '/dresscode.phar';
	private const Served = ['DressCode\\', 'PhpSyntax\\', 'Nette\\Schema\\', 'PHPStan\\PhpDocParser\\', '_DressCode\\'];

	/** @var ?array<string, list<string>> */
	private static ?array $psr4 = null;

	/** @var ?array<string, string> */
	private static ?array $classMap = null;


	public static function loadClass(string $class): void
	{
		if (
			defined('__DRESSCODE_RUNNING__')
			|| !array_any(self::Served, fn($prefix) => str_starts_with($class, $prefix))
		) {
			return;
		}

		if (!in_array('phar', stream_get_wrappers(), true)) {
			throw new \RuntimeException('DressCode is distributed as a phar and needs the phar stream wrapper; check the phar extension in php.ini.');
		}

		if (self::$psr4 === null) {
			self::$psr4 = require self::Phar . '/vendor/composer/autoload_psr4.php';
			self::$classMap = require self::Phar . '/vendor/composer/autoload_classmap.php';
			foreach (require self::Phar . '/vendor/composer/autoload_files.php' as $id => $file) {
				if (empty($GLOBALS['__composer_autoload_files'][$id])) {
					$GLOBALS['__composer_autoload_files'][$id] = true;
					require $file;
				}
			}
		}

		if (isset(self::$classMap[$class])) {
			require self::$classMap[$class];
			return;
		}

		foreach (self::$psr4 as $prefix => $dirs) {
			if (str_starts_with($class, $prefix)) {
				$relative = strtr(substr($class, strlen($prefix)), '\\', '/') . '.php';
				foreach ($dirs as $dir) {
					if (is_file("$dir/$relative")) {
						require "$dir/$relative";
						return;
					}
				}
			}
		}
	}
}

spl_autoload_register([PharAutoloader::class, 'loadClass']);
