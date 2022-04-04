# Django settings

For more information on django settings, see https://docs.djangoproject.com/en/3.2/topics/settings/.
For the full list of settings and their values, see https://docs.djangoproject.com/en/3.2/ref/settings/.

The structure of the django settings module is the following:

- the `_internal.py` module contains application settings for django.
- the `_schema.py` module contains the schema for the user configuration parsing and validation.
- the `prod.py` (`libretime_api.settings.prod`) module is the django settings entrypoint. The module contains bindings between the user configuration and the django settings. **Advanced users** may edit this file to better integrate the LibreTime API in their setup.
- the `testing.py` (`libretime_api.settings.testing`) module is the testing django settings entrypoint.
