<?php

namespace App\Helpers {

    use App\StaticFactory;

    /**
     * Class Base
     * @method static Base config
     * @package App\Helpers
     */
    class Base extends StaticFactory
    {
        public static $config = [];

        public function inject()
        {
        }

        public function configFactory()
        {
            $this->inject();
        }

    }
}