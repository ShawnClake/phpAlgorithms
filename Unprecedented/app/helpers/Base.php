<?php

namespace App\Helpers {

    use App\StaticFactory;

    /**
     * Class Base
     * @method static Base config()
     * @package App\Helpers
     */
    abstract class Base extends StaticFactory
    {
        /** @var string[] */
        public static $config = [];

        /**
         * Override this to allow other helper classes to inject config entries
         */
        abstract public function inject();

        /**
         *
         */
        public function configFactory()
        {
            $this->inject();
        }

    }
}