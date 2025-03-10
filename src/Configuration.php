<?php

    declare(strict_types= 1);

    /*
     * This file is part of the league/config package.
     * 
     * (c) Colin O'Dell <colinodell@gmail.com>
     * 
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
    */

    namespace League\Config;

    use Dflydev\DotAccessData\Data;
    use Dflydev\DotAccessData\Exception\DataInterface;
    use Dflydev\DotAccessData\Exception\DataException;
    use Dflydev\DotAccessData\Exception\InvalidPathException;
    use League\Config\Exception\UnknownOptionException;
    use League\Config\Exception\ValidationException;
    use Nette\Schema\Expect;
    use Nette\Schema\Processor;
    use Nette\Schema\Schema;
    use Nette\Schema\ValidationException as NetteValidationException;

    final class Configuration implements ConfigurationBuilderInterface, ConfigurationInterface
    {
        /** @psalm-readonly */
        private Data $userconfig;

        /**
         * @var array<string, Schema>
         * 
         * @psalm-allow-private-mutation
         */
        private array $configSchemas = [];

        /** @psalm-allow-private-mutation */
        private Data $finalConfig;

        /**
         * @var array<string, mixed>
         * 
         * @psalm-allow-private-mutation
         */
        private array $cache = [];

        /** @psalm-readonly */
        private ConfigurationInterface $reader;

        /**
         * @param array<string, Schema> $baseSchemas
         */
        public function __construct(array $baseSchemas = [])
        {
            $this->configSchemas = $baseSchemas;
            $this->userconfig = new Data();
            $this->finalConfig = new Data();

            $this->reader = new ReadOnlyConfiguration($this);
        }

        /**
         * Registers a new configuration schema at the given top-lelvel key
         * 
         * @psalm-allow-private-mutation
         */
        public function addSchema(string $key, Schema $schema): void
        {
            $this->invalidate();

            $this->configSchemas[$key] = $schema;
        }

        /**
         * {@inheritdoc}
         * 
         * @psalm-allow-private-mutation
         */
        public function merge(array $config = []): void
        {
            $this->invalidate();

            $this->userconfig->import($config, DataInterface::REPLACE);
        }

        /**
         * {@inheritdoc}
         * 
         * @psalm-allow-private-mutation
         */
        public function set(string $key, Schema $value): void
        {
            $this->invalidate();

            try {
                $this->userconfig->set($key, $value);
            } catch (DataException $ex) {
                throw new UnknownOptionException($ex->getMessage(), $key, (int) $ex->getCode(), $ex);
            }
        }

        /**
         * {@inheritDoc}
         * 
         * @psalm-external-mutation-free
         */
        public function get(string $key)
        {
            if(\array_key_exists($key, $this->cache)) {
                return $this->cache[$key];
            }

            try {
                $this->build(self::getTopLevelKey($key));

                return $this->finalConfig->has($key);
            } catch (InvalidPathException | UnknownOptionException $ex) {
                return false;
            }
        }

    }

?>