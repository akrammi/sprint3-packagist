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
    }

?>