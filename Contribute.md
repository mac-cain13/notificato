# Thank you!

Thank you for taking some of your precious time helping this project move forward. Really great that you're showing interest in contributing.

This guide will help you get started with contributing to Notificato. You don't have to be an expert to help us, every small tweak, crazy idea and/or bugreport is highly appreciated!

# Contributing

## Team members

* Mathijs Kadijk / [mac-cain13](https://github.com/mac-cain13) - Lead development
* Rick Pastoor / [rickpastoor](https://github.com/rickpastoor) - Development (also initiator of the [Symfony2 bundle](https://github.com/wrep/notificato-symfony))

## Learn & listen

This section includes ways to get started with this open source project. Most important is to read the docs and scan the issue tracker before so you're sure your idea/bugreport/patch isn't already in the make/being fixed:

* [Readme.md](Readme.md)
* [Notificato Documentation](doc/Readme.md)
* [Notificato API Documentation](http://wrep.github.com/notificato/master/)
* [Issue tracker](https://github.com/wrep/notificato/issues)

## Adding new features

This section includes some advice on how to build new features & what kind of you should .

* Fork the project and **make you changes against the development branch**.
* Follow the currently used coding style
* Make sure the inline code comments are up to date
* Write a test for the new features
* Make sure all tests pass
* Submit a pull request and clearly state what you've added!

Don't know if your test is good enough or not sure your change is good enough? Don't hesitate to submit an issue or send the incomplete pull request. We'll take a look, point you in the right direction or make some corrections, no problem!

So don’t get discouraged! We estimate that the response time from the maintainers is around: 2/3 days

# Bug triage

* You can help report bugs by filing them here: https://github.com/wrep/notificato/issues/new
* You can look through the existing bugs here: https://github.com/wrep/notificato/issues

* Look at existing bugs and help us understand if:
** The bug is reproducible? What are the steps to reproduce?

* Tips for a great bugreport:
    * State what you expected
    * Describe what happend instead
    * Give the steps to reproduce the bug
    * Include your PHP version
    * Include the Notificato version your using
    * If you know a workaround/fix please include it

# Documentation

Code needs explanation, and sometimes those who know the code well have trouble explaining it to someone just getting into it. If you find something unclear, incorrect or missing from the documentation please file a bugreport!

* You can help us improve the documentation by:
    * Filing a bugreport about your problem
    * Forking the project and submit a pull request with expanded/corrected docs
    * Typo corrections are also welcome

# Community
If you write a blog about Notificato, use it in your project, recommended it to a friend or feel it completes your life, please feel free to send me a mail at mkadijk@gmail.com. I'll see if I can mention projects/blogs somewhere and it's very motivating to hear something from happy users.

*Please don't mail me for support, but use the issuetracker and StackOverflow/forums for that so more people can help, thanks!*

# Your first bugfix
A very quick and dirty overview of the steps to take to submit your first bugfix:

* Click fork here on Github and check out the repository
* Run `composer install --dev` to install dependencies
* Run the tests with `php ./vendor/bin/phpunit` (To check if anything is broken atm)
* Go code your bugfix/feature and use the tests to test if it works
* Make sure you've done the things stated in "Adding new features"
* Commit and push it back to your fork
* Click the "Pull request"-button and write a nice message what you've done
* Wait for us to reply! :)