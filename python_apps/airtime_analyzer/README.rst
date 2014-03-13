
Ghetto temporary installation instructions
==========

    $ sudo python setup.py install

You will need to allow the "airtime" RabbitMQ user to access all exchanges and queues within the /airtime vhost:

    sudo rabbitmqctl set_permissions -p /airtime airtime .* .* .* 


Usage
==========

To print usage instructions, run:

    $ airtime_analyzer --help

This application can be run as a daemon by running:

    $ airtime_analyzer -d



Developers
==========

For development, you want to install airtime_analyzer system-wide but with everything symlinked back to the source 
directory for convenience. This is super easy to do, just run:
    
    $ sudo python setup.py develop

To send an test message to airtime_analyzer, you can use the message_sender.php script in the tools directory.
For example, run:

    $ php tools/message_sender.php '{ "tmp_file_path" : "foo.mp3", "final_directory" : ".", "callback_url" : "http://airtime.localhost/rest/media/1", "api_key" : "YOUR_API_KEY" }'

Logging
=========

By default, logs are saved to:

    /var/log/airtime/airtime_analyzer.log

This application takes care of rotating logs for you.


Unit Tests
==========

To run the unit tests, execute:

    $ nosetests

If you care about seeing console output (stdout), like when you're debugging or developing
a test, run:

    $ nosetests -s


