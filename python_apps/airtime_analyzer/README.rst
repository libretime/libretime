
Ghetto temporary installation instructions

set up a virtualenv
activate it
pip install mutagen python-magic pika

You will need to allow the "airtime" RabbitMQ user to access the airtime-uploads exchange and queue:

    sudo rabbitmqctl set_permissions -p /airtime airtime airtime-uploads airtime-uploads airtime-uploads


Developers
==========

For development, you want to install AAQ system-wide but with everything symlinked back to the source 
directory (for convenience), so run:
    
    $ sudo python setup.py develop



Unit Tests
==========

To run the unit tests, execute:

    $ nosetests


