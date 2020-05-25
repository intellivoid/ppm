#!/bin/bash
# ------------------------------------------------------------------
# [Zi Xing Narrakas] PHP Package Manager
#          PHP Package Manager (PPM) is a system-based package
#          and dependency manager for PHP libraries and components
#          this tool allows you to compile your PHP libraries into
#          a redistributable ppm file and install it on various
#          systems in which can be utilized by PHP packages
#
#          https://github.com/intellivoid/ppm
#
# Dependency:
#     PHP 7.*
# ------------------------------------------------------------------

# Global Variables
PPM_INSTALL_DIRECTORY="/var/ppm"
PPM_SOURCE_DIRECTORY="./src/ppm"
PPM_MAIN="./ppm.sh"
USER_BIN="/usr/bin"

# Check if the source directory exists
if [ ! -d "${PPM_SOURCE_DIRECTORY}" ]
then
    echo "The path '${PPM_SOURCE_DIRECTORY}' does not exist"
    exit 1
fi

# Check if permissions are present
if [ "$EUID" -ne 0 ]
  then echo "This operation requires root privileges, run as sudo."
  exit 1
fi

# Start the installation
echo "Installing PPM"

# Delete the old installation
if [ -d "${PPM_INSTALL_DIRECTORY}" ]
then
    rm -rf "${PPM_INSTALL_DIRECTORY}"
fi

# Copy the files from source to the new directory
mkdir "${PPM_INSTALL_DIRECTORY}"
cp -r "${PPM_SOURCE_DIRECTORY}/." "${PPM_INSTALL_DIRECTORY}/"

# Create a link
cp "${PPM_MAIN}" "${PPM_INSTALL_DIRECTORY}/ppm.sh"
chmod +x "${PPM_INSTALL_DIRECTORY}/ppm.sh"

if [ -f "${USER_BIN}/ppm" ]
then
    rm "${USER_BIN}/ppm"
fi

ln -s "${PPM_INSTALL_DIRECTORY}/ppm.sh" "${USER_BIN}/ppm"

echo "Installation completed"