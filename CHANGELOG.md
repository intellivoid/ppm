# v1.0.0.0
First initial release of PPM for production & commercial use.


# v1.0.0.1
This update introduces minor bug fixes and optimizations

- Added "files" to the package structure to allow for non-php files to be included in a package
- Updated package generator to discover non-php files to include in the "files" property of a package
- Partially corrected PackageManager to use options instead of bare-parameters
- Added option "no_details" in PackageManager to omit package details during an update
- Bug fix PackageManager when updating all packages and one package is not updated due to hard_failure not being set
  to "False" causing the update to fail

This update does not fix the concurrent issues ongoing with Windows, PPM is not yet optimized for Windows and no
uninstaller is made available yet. Follow the documentation on how to uninstall PPM.

Updating PPM from 1.0.0.0 to 1.0.0.1 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.0.0.2
This update introduces minor bug fixes and optimizations

- Bug fix in \ppm\Utilities\CLI > Tools > generatePackageJson() where Components and Files not being empty
- Made PPM ASCII logo more whacky

Updating PPM from 1.0.0.0 to 1.0.0.1 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.0.0.3
This update introduces bug fixes

- Fixed AutoIndexer cache permission issue
- Fixed compiler issue where when compiling mal-formed UTF-8 files fails, fixed the issue using a fallback
  byte-compiler method which will now introduce the header "byte_compiled" in a compiled file
  which will contain all the byte-compiled files instead of holding them in the "compiled_components" header

Updating PPM from 1.0.0.+ to 1.0.0.3 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected. Packages that failed to install correctly
due to the compiler bug will be required to be recompiled so that the issue can be corrected.


# v1.0.0.4
This update introduces optimizations and bug fixes

- The import function will now recursively import dependencies
- PPM Will store the package lock configuration in memory rather than loading it each time from disk
- Already-imported warnings are suppressed by default

Updating PPM from 1.0.0.+ to 1.0.0.4 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.0.0.5
This update introduces optimizations and bug fixes

- Fixes PackageLock write cache issue

Updating PPM from 1.0.0.+ to 1.0.0.4 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.0.0.6
This update introduces optimizations and bug fixes

- Adds option --clear-cache
- By default, the cache is disabled.

Updating PPM from 1.0.0.+ to 1.0.0.6 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.1.0.0
This update introduces a lot of improvements and new features

- Added DirectoryScanner Class, ported from theseer
- Added AutoloaderBuilder port from composer
- Updated PHPDoc for AutoloadMethod.php explaining the purpose of the autoloading method and the cons
- Added autoload method "generated_static" (GeneratedStatic)
- Added autoload method "generated_spl" (StandardPhpLibrary)
- Added unit autoload generator
- Changed import logic to import dependencies first before importing the requested package
- Updated --clear-cache command to clear the build and repo cache folders

Updating PPM from 1.0.0.+ to 1.1.0.0 simply requires you to run the installer, previous package installations/packages
will be backwards compatible with this version and it will work as expected.


# v1.1.0.1
This update introduces more compiler options, minor bug fixes and the ability to install composer packages
as PPM packages natively without needing to install composer on the system.

- Updated the help menu to categorize the options and flags under sub-menus
- Added option 'alm'
- Added option 'lerror' and 'lwarning' for linting checks
- Added option 'bcerror' and 'bcwarning' for byte-compiling
- Added option 'cerror' and 'cwarning' for general compiler error handling
- Updated the install script to install composer in the PPM installation directory
- Added support for composer, you can install packages with this source syntax "vendor_name@composer/package_name"
- GitHub vault now has a default profile, allowing you to install packages using the syntax "default@github/org/repo"
- Added command 'github-set-default' to change the default alias used
- Updated the package dependency standard to specify how PPM can obtain missing dependencies using remote sources "source"
- The package generator tool will not overwrite the autoloading method in the package configuration
- Added compatibility tools for composer to convert composer package traits to PPM
- Updated the version validation method to allow version formats with at least 2 parts, "1.0", "1.0.0" and "1.0.0.0" are
  now accepted as valid version numbers
- Bug fix in the import function when attempting to import non-required dependencies that aren't installed
- Added a new unusable autoloading method called "composer_generated" which is intended to use autoloader files generated by composer
- Made autoloader generators more tolerable towards duplication errors
- The clear cache tool will now clear composer cache data too
- Added option 'install-native' for installing composer packages, this command will attempt to automatically generate
  and compile each package installed by composer as a native PPM package. This can cause unexpected issues while
  importing these packages because not all components may be compiled or imported correctly.
- Updating packages will now update composer packages and their native install trait


# v1.1.0.2
This update introduces minor bug fixes and addition to missing features

- Corrected Composer package name to PPM package name converter
- Added option "--github-set-default" to change the default GitHub profile
- Bug fix in PackageManager when installing a dependency that has already been satisfied results in a "Path does not exist"
  error because of a missing return statement.
- Updated logging styles to be more basic
- Added option "sdc" to parse detailed information about a .ppm file


# v1.1.0.3
This update introduces the ability to self-update using the command '--update-ppm' and adds missing docstrings to IO


# v1.1.0.4
This update introduces a minor bug fixes

- Corrected updating from different sources bug
- Updated help menu format


#v1.1.0.5
This update introduces a minor bug fix

- Minor correction in PackageLockItem where versions would be treated as object-arrays than list-arrays


#v1.1.2.0
Major update, fixes compatibility issues with PHP 8.0, updated PpmParser to 2.0.0.0


#v1.1.2.1
Fixed the autoloader generator for PHP8.0


#v1.1.2.2
Bug fix for broken symlinks


#v1.1.2.3
This update brings some minor improvements to the compiler

- The ability to skip dependencies when installing a package
- Added option 'runtime-version' for --main to specify what PHP version to run


#v1.1.3.0
This update brings performance & optimization updates to PPM & The PPM Compiler, additional features are also
added to this release

- Reconstructed the PPM autoloader to load components depending on the runtime environment to optimize on performance,
  for instance; when using PPM in real-time only the core components will be loaded, when using PPM via the command-line
  the additional components including the compiler will be loaded in
- Updated the PPM extension for pear (1.0.1) to use the new autoloader feature accordingly
- Optimized and corrected compiler issues for PHP 8.1 & Code handling
- Fixed precedence of arrow functions.
- Non-UTF-8 code units in strings will now be hex-encoded.
- [PHP 8.1] Added support for enums. These are represented using the Stmt\Enum_ and Stmt\EnumCase nodes.
- [PHP 8.1] Added support for never type. This type will now be returned as an Identifier rather than Name.
- Added ClassConst builder.
- Builder methods for types now property handle never type.
- PrettyPrinter now prints backing enum type.
- NameResolver now handles enums.
- An addAttribute() method accepting an Attribute or AttributeGroup has been added to all builders that accept attributes, such as Builder\Class_.
- BuilderFactory::attribute() has been added.
- BuilderFactory::args() now accepts named arguments.


#v1.1.3.1
This update brings CI (GitHub only for now) to PPM.

- Added "--generate-ci" which generates ci scripts which runs a compiling test against your package, you can use
  the traditional arguments such as "--branch" and "--runtime-version" to alter the environment
- Added "--generate-ci-release" which generates ci scripts that compiles your package and creates a .ppm release file


#v1.1.3.2
Added function 'import' which acts like an alias for ppm\ppm::import()