# libretime-analyzer

libretime-analyzer is a daemon that processes LibreTime file uploads as background jobs.

It performs metadata extraction using Mutagen and moves uploads into LibreTime's
music library directory (stor/imported).

libretime-analyzer uses process isolation to make it resilient to crashes and runs in
a multi-tenant environment with no modifications.

## Installation

```bash
python setup.py install
```

You will need to allow the "airtime" RabbitMQ user to access all exchanges and queues within the /airtime vhost:

```bash
rabbitmqctl set_permissions -p /airtime airtime .\* .\* .\*
```

## Usage

This program must run as a user with permissions to write to your Airtime music library
directory. For standard Airtime installations, run it as the www-data user:

```bash
sudo -u www-data libretime-analyzer --daemon
```

Or during development, add the --debug flag for more verbose output:

```bash
sudo -u www-data libretime-analyzer --debug
```

To print usage instructions, run:

```bash
libretime-analyzer --help
```

This application can be run as a daemon by running:

```bash
libretime-analyzer -d
```

# Developers

For development, you want to install libretime-analyzer system-wide but with everything symlinked back to the source
directory for convenience. This is super easy to do, just run:

```bash
pip install -r requirements-dev.txt
pip install --editable .
```

To send an test message to libretime-analyzer, you can use the message_sender.php script in the tools directory.
For example, run:

```bash
php tools/message_sender.php '{ "tmp_file_path" : "foo.mp3", "final_directory" : ".", "callback_url" : "http://airtime.localhost/rest/media/1", "api_key" : "YOUR_API_KEY" }'

php tools/message_sender.php '{"tmp_file_path":"foo.mp3", "import_directory":"/srv/airtime/stor/imported/1","original_filename":"foo.mp3","callback_url": "http://airtime.localhost/rest/media/1", "api_key":"YOUR_API_KEY"}'
```

## Logging

By default, logs are saved to:

```
/var/log/airtime/airtime_analyzer.log
```

This application takes care of rotating logs for you.

## Unit Tests

To run the unit tests, execute:

```bash
nosetests
```

If you care about seeing console output (stdout), like when you're debugging or developing
a test, run:

```bash
nosetests -s
```

To run the unit tests and generate a code coverage report, run:

```bash
nosetests --with-coverage --cover-package=airtime_analyzer
```

## Running in a Multi-Tenant Environment

## History and Design Motivation
