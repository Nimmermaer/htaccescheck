<?php

namespace Iwmedien\Htaccescheck\Utilities;

class RemoveDuplicates
{
        /**
         * @return mixed[]
         */
        public static function run($csvAsArray): array
        {
            return array_values($csvAsArray);
        }
}