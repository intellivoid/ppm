<?php


    namespace PpmPython\Utilities;

    use ppm\Utilities\Validate;
    use PpmPython\Exceptions\ParserException;
    use PpmPython\Exceptions\PipException;
    use PpmPython\Objects\PipInstall;

    /**
     * Class Parser
     * @package PpmPython\Utilities
     */
    class Parse
    {
        /**
         * Parses the pip version string and determines the version and install location
         *
         * @param string $input
         * @return PipInstall
         * @throws ParserException
         */
        public static function pipVersionString(string $input): PipInstall
        {
            $input = str_ireplace("\n", "", $input);
            $input = str_ireplace("\r", "", $input);

            $exploded = explode(" ", $input);

            // Verify argument count
            if(count($exploded) < 4)
                throw new ParserException("Cannot parse pip version string '$input', too little arguments, expected more than 4, got " . count($exploded));

            // Verify prefix
            if(strtolower($exploded[0]) !== "pip")
                throw new ParserException("Cannot parse pip version string '$input', expected prefix 'pip', got '" . $exploded[0] . "'");

            if(Validate::Version($exploded[1]) == false)
                throw new ParserException("Cannot parse pip version string '$input', Invalid version number, got '" . $exploded[1] . "'");

            $pipInstallObject = new PipInstall();
            $pipInstallObject->Version = $exploded[1];

            foreach($exploded as $item)
            {
                if(file_exists($item))
                {
                    $pipInstallObject->InstallPath = $item;
                    break;
                }
            }

            return $pipInstallObject;
        }

        /**
         * Parses the python version string and determines the version
         *
         * @param string $input
         * @return string
         * @throws ParserException
         */
        public static function pythonVersionString(string $input): string
        {
            $input = str_ireplace("\n", "", $input);
            $input = str_ireplace("\r", "", $input);

            $exploded = explode(" ", $input);

            // Verify argument count
            if(count($exploded) < 2)
                throw new ParserException("Cannot parse python version string '$input', too little arguments, expected 2 or more, got " . count($exploded));

            // Verify prefix
            if(strtolower($exploded[0]) !== "python")
                throw new ParserException("Cannot parse python version string '$input', expected prefix 'python', got '" . $exploded[0] . "'");

            if(Validate::Version($exploded[1]) == false)
                throw new ParserException("Cannot parse python version string '$input', Invalid version number, got '" . $exploded[1] . "'");

           return $exploded[1];
        }
    }