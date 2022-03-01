# Django settings

The structure of the django settings module is the following:

- the `__init__.py` (`libretime_api.settings`) module is the django settings entrypoint. The module contains bindings between the user configuration and the django settings. **Advanced users** may edit this file to better integrate the LibreTime API in their setup.
- the `_internal.py` module contains application settings for django.
- the `_schema.py` module contains the schema for the user configuration parsing and validation.
