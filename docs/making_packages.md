# Making PPM packages

You can turn your existing code into a PPM package simply by adding
two files to your project. `.ppm_package` or `.ppm` and `package.json`


## .ppm_package / .ppm (Path Pointer File)

This file is simply a pointer to the directory containing the project
source code and package.json file, the name `.ppm_package` and `.ppm`
serves the same purpose.

When you run `ppm --compile` without the specified path, it will look
for a `.ppm` or `.ppm_package` which points to the specified path to
look for a `package.json` file.

The contents of a path pointer file looks something like this

```
src/MyLibrary
```

In this scenario, the folder `src/MyLibrary` contains all the .php
source code files, and the package.json file (`src/MyLibrary/package.json`)
and using this information is what ppm will work with.

This file is completely optional and only intended to make Makefiles
easier to work with

```makefile
build:
	mkdir build
	ppm --compile --directory="build"

install:
	ppm --no-prompt --install="build/com.intellivoid.mongodb_driver.ppm"
```


## package.json

This is the main file that PPM will use to compile your code. if it
doesn't exist then it isn't built for PPM. This file will tell PPM
what your package is called, who created it, what version your code
is, what packages it depends on and what your package consists of.

```json
{
  "ppm_version": "1.0.0.0",
  "package": {
    "package_name": "com.intellivoid.mongodb_driver",
    "name": "MongoDB Driver",
    "version": "1.6",
    "author": "Zi Xing Narrakas",
    "organization": "Intellivoid Technologies",
    "description": "Low-level driver for MongoDB",
    "url": "https://github.com/Intellivoid/Mongo-Driver",
    "dependencies": [
      {"package": "com.intellivoid.acm", "version": "latest", "required": false}
    ],
    "configuration": {
      "autoload_method": "indexed",
      "main": {
        "execution_point": "ppm/cli_example.php",
        "create_symlink": true,
        "name": "ppm_example"
      },
      "post_installation": [
        "ppm/post_example.php"
      ],
      "pre_installation": [
        "ppm/before_example.php"
      ]
    }
  },
  "components": [
    {"required": true, "file": "Exception/Exception.php"},
    {"required": true, "file": "Exception/RuntimeException.php"},
    {"required": true, "file": "Exception/BadMethodCallException.php"},
    {"required": true, "file": "Exception/InvalidArgumentException.php"},
    {"required": true, "file": "Exception/UnsupportedException.php"},

    {"required": true, "file": "GridFS/Exception/CorruptFileException.php"},
    {"required": true, "file": "GridFS/Exception/FileNotFoundException.php"},
    {"required": true, "file": "GridFS/Bucket.php"},
    {"required": true, "file": "GridFS/CollectionWrapper.php"},
    {"required": true, "file": "GridFS/ReadableStream.php"},
    {"required": true, "file": "GridFS/StreamWrapper.php"},
    {"required": true, "file": "GridFS/WritableStream.php"}
  ]
}
```

This is what a package.json file's structure would look like, later
or earlier versions may change the format of this file but backwards
compatibility will always be present. It is recommended to always
update your package.json structure when the standard updates to
eliminate potential warnings that PPM may return when compiling.




### Structure

The main structure of the package, containing information about how
to compile and install the package

| Name        | Type                            | Required | Description                                                                                  |
|-------------|---------------------------------|----------|----------------------------------------------------------------------------------------------|
| ppm_version | string                          | Yes      | The version of PPM that this package is built for                                            |
| package     | [PackageInfo](#PackageInfo)[]   | Yes      | Contains information about the package metadata, dependencies and installation configuration |
| components  | [Component](#Component)[]       | Yes      | The .php components that PPM should compile and include in the package.                      |


### PackageInfo

Metadata of the package including required dependencies and configuration
information about how the package should be installed

| Name          | Type                                            | Required | Description                                                       |
|---------------|-------------------------------------------------|----------|-------------------------------------------------------------------|
| package_name  | string                                          | Yes      | The name of the package (com.organization.name)                   |
| name          | string                                          | Yes      | User-friendly name of the package                                 |
| version       | string                                          | Yes      | The version of the package (See version structure)                |
| author        | string                                          | Yes      | The author(s) of the package                                      |
| organization  | string                                          | Yes      | The organization holder (None if there's no organization)         |
| description   | string                                          | Yes      | User-friendly long-text description of what this package is about |
| url           | string                                          | Yes      | The URL/Repoistory of this package                                |
| dependencies  | [Dependency](#Dependency)[]                     | Yes      | Array of dependents that this package uses/requires               |
| configuration | [PackageConfiguration](#PackageConfiguration)[] | Yes      | Indicates how this package is installed and used on the system    |


### Component

General component of the package, this does not apply to data files and is
only applicable to .PHP files, this is what PPM will use to compile and 
pack the component into the package. Files with invalid syntax will cause the
compiler to throw errors.

| Name     | Type    | Required | Description                                                                                                                   |
|----------|---------|----------|-------------------------------------------------------------------------------------------------------------------------------|
| required | boolean | Yes      | A required component, will cause the compiler to throw an error if the component is missing                                   |
| file     | string  | Yes      | The location of the file from the current location from where package.json is located in (eg; Exception/RuntimeException.php) |


### Dependency

Dependency configuration for the package, this is used by PPM during the
installation procedure and checks if the dependency requirement is met before
proceeding with the installation

| Name     | Type    | Required | Description                                                                                                                                                 |
|----------|---------|----------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| package  | string  | Yes      | The name of the package (eg; com.intellivoid.acm)                                                                                                           |
| version  | string  | Yes      | The required version of the package. Use "latest" so it will only apply to the latest version of the package even if there are multiple versions installed. |
| required | boolean | Yes      | Indicates if this dependency is required or optional.  If a required dependency is missing then the install will fail                                       |


### PackageConfiguration

The package configuration that indicates how the package is installed on the
system including how the package autoloader is initialized upon import.

| Name              | Type                                        | Required | Description                                                                                                                                                                                         |
|-------------------|---------------------------------------------|----------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| autoload_method   | string                                      | Yes      | The method of autoloader that should be used (`static`, `indexed`)                                                                                                                                  |
| main              | [MainExecutionConfig](#MainExecutionConfig) | No       | If your package has a main program that can be executed from the commandline, then it can be configured here so that you can run code from ppm `ppm --main="<package_name>" --args="--foo=\"bar\""` |
| post_installation | string[]                                    | Yes      | PHP Scripts to execute after the installation                                                                                                                                                       |
| pre_installation  | string[]                                    | Yes      | PHP Scripts to execute prior to the installation                                                                                                                                                    |


### MainExecutionConfig

The MainExecutionConfig is a object that defines how code from your package
is executed from the commandline like an independent program. 

| Name            | Type    | Required | Description                                                                                                                                        |
|-----------------|---------|----------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| execution_point | string  | Yes      | The main .PHP file to execute                                                                                                                      |
| create_symlink  | boolean | Yes      | If true, PPM will register a symbolic link to the main execution point on the system so that it can be executed from the commandline without `ppm` |
| name            | string  | Yes      | The name of the command if `create_symlink` is enabled. If none, leave the value at `null`                                                         |


## Compiling package

Once that your package.json file has been structured to include all the
components, package information and whatnot then you can compile your package
into a redistributable .ppm file.

The file package.json is located in "src/MyLibrary" so pointing
the compiler to that directory will allow ppm to locate package.json

```shell script
ppm --compile="src/YourLibrary"
```


If your current working directory contains a `.ppm` or `.ppm_package` file
(also known as a Path Pointer File) then simply running `--compile` will
achieve the same as the example above

```shell script
ppm --compile
```

if you want the .ppm file to be saved in a seperate directory rather than
the current working directory then provide the `--directory` option

```shell script
ppm --compile="src/YourLibrary" --directory="build"
```