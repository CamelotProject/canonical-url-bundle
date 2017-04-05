# Contributing

## Reporting an issue
If you've spotted a bug or would like to see something added but aren't comfortable writing your own code then 
by all means create an issue and ask. Please do check that your request hasn't been asked before by searching for open and closed
issues beforehand.

## Creating a pull request
Pull requests are very welcome. Please use the standard 'fork and pull' GitHub model:

1. Fork the repository and and clone locally
2. Run `composer install`
3. Create a new branch for your changes and commit them.
4. Open a pull request

All pull requests must pass travis CI builds so please ensure your code follows PSR2 standards and passes all unit tests.
All tests can be run from the command line by running the following command:
```bash
./vendor/bin/phpunit -v
```

If your IDE doesn't support PHP CodeSniffer you can run the following command to test all code against PSR2:
```bash
find -L .  -path ./vendor -prune -o -name '*.php' -print0 | xargs -0 -n 1 -P 4 ./vendor/bin/phpcs --standard="PSR2" -v
```

Any new features should also include an accompanying unit test located within the relevant directory within `Tests`.
