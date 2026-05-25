# LibreTime Frontend

This is the new UI that will replace the legacy PHP code currently accessible
when LibreTime is accessed on the web. This is a Vue 3 application that uses
Vuetify for the UI components. All package management is done with `yarn`.

THIS IS NOT PRODUCTION READY. This is still a work in progress.

## Usage

This section covers how to start the development server and build your project for production.

### Starting the Development Server

To start the development server with hot-reload, run the following command. The server will be accessible at [http://localhost:3000](http://localhost:3000):

```bash
yarn install
yarn dev
```

> Add NODE_OPTIONS='--no-warnings' to suppress the JSON import warnings that happen as part of the Vuetify import mapping. If you are on Node [v21.3.0](https://nodejs.org/en/blog/release/v21.3.0) or higher, you can change this to NODE_OPTIONS='--disable-warning=5401'. If you don't mind the warning, you can remove this from your package.json dev script.

### Building for Production

To build your project for production, use:

```bash
yarn build
```

Once the build process is completed, your application will be ready for deployment in a production environment.

### Storybook

To run the storybook server, run the following command. This will open the storybook server at
[http://localhost:6006](http://localhost:6006):

```bash
yarn storybook
```

### Tests and Linting

To run the tests and linting, use:

```bash
yarn lint
npx prettier -w src
yarn cypress run # To run end-to-end tests
yarn cypress open # to develop tests
```

## Translations

Translations are handled through JSON files in `src/locales/`. The files use
nested keys to describe individual translations.
