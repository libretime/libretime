# Libretime Vue UI Development

## Project setup

We use yarn for package management for this Node.js project. If you don't have yarn, install it with

```
npm i -g yarn
```

## Project commands

```
# install all packages
yarn

# start dev environment
yarn dev

# build
yarn build

# run Eslint
yarn lint

# run Prettier, writing formatting changes to files
npx prettier -w src

# run tests with Cypress
yarn cypress:run
```

## Translation files

Ultimately, Weblate will be set up to translate strings in the new UI; for now, generated `.json` files are available in `src/locale`. The `.json` translations are decoupled from Weblate, and so Weblate updates will not update this part of the repo. These are just provided for development purposes.

## MPA file structure

This folder is split up into multiple apps.

-   Main: `index.html` endpoint, `/` in the browser
-   About: `src/about/index.html` endpoint, `/src/about` in the browser

Once built into static pages, the URL paths will be the same as the folder structure mentioned above. Both apps can use the same plugins, components, translations, etc., they just have different endpoints so we can statically build them separately.
