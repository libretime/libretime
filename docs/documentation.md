Documentation
=============

The LibreTime documentation site is generated with [mkdocs](http://www.mkdocs.org/). To get started contributing to this project, fork it on Github. Then install mkdocs and clone this repo locally:

    :::bash
    sudo brew install python              # For OSX users
    sudo aptitude install python-pip      # For Debian/Ubuntu users
    sudo pip install mkdocs
    git clone https://github.com/libretime/libretime
    cd libretime
    git remote add sandbox https://github.com/<username>/libretime   # URL for your fork
    mkdocs build --clean
    mkdocs serve

Your local LibreTime docs site should now be available for browsing: [http://localhost:8888/](http://localhost:8888/).

When you find a typo, an error, unclear or missing explanations or instructions, open a new terminal and start editing. Your changes should be reflected automatically on the local server. Find the page you’d like to edit; everything is in the docs/ directory. Make your changes, commit and push them, and start a pull request:

    :::bash
    git checkout -b fix_typo
    vi docs/index.md                      # Add/edit/remove whatever you see fit. Be bold!
    mkdocs build --clean; mkdocs serve    # Go check your changes. We’ll wait...
    git diff                              # Make sure there aren’t any unintended changes.
    git commit -am”Fixed typo.”           # Useful commit message are a good habit.
    git push sandbox fix_typo

Visit your fork on Github and start a PR.
