# Redirect Manager

Managers URL- and page-redirects from a TYPO3 backend module. This package works in combination with the TYPO3 core extension "redirects".

After installation, a new backend-module after the TYPO3 core extension *redirects* will be present. All features are found in there. Make sure to visit the
extension configuration in the install tool to setup all desired features correctly!

# Features

## Custom `PageErrorHandler`

A custom `PageErrorHandler` is provided which can be used by setting it according to the TYPO3 documentation in the site configuration YAML. This error handler
adds additional logging and is needed by some other features to work correctly. No personal data is logged at any time by this error handler, only statistical
information about what URL e.g. produced a 404-error.

## List "404 Not Found"

With the help of the custom `PageErrorHandler`, all "404 Not Found"-requests can be logged into a database table. They will then be listed in the backend module
including a hit-counter and a button to the sysext *redirects*.

This can help in finding dead links or incorrect redirects.
