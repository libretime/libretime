# Legacy locales

To add a new locale, make sure to add/edit the following files:

- `legacy/application/models/Locale.php`
- `legacy/locale/<LANG>/LC_MESSAGES/libretime.po`
- `legacy/public/js/datatables/i18n/<LANG>.txt`
- `legacy/public/js/plupload/i18n/<LANG>.js`

The `legacy/application/controllers/LocaleController.php` contains additional translations loaded by jquery i18n `$.i18n` and used with `$.i18n._`.
