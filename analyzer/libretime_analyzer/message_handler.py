from kombu import Connection, Exchange, Message, Queue
from kombu.mixins import ConsumerProducerMixin
from loguru import logger
from pydantic import ValidationError

from .report import report_to_callback
from .pipeline import Context, run_pipeline

ANALYZER_EXCHANGE = Exchange(
    name="analyzer",
    type="topic",
)

PIPELINE_ROUTING_KEY = "analyzer.pipeline"
PIPELINE_QUEUE = Queue(
    name="analyzer.pipeline",
    exchange=ANALYZER_EXCHANGE,
    routing_key=PIPELINE_ROUTING_KEY,
)

RESULT_ROUTING_KEY = "analyzer.result"
RESULT_QUEUE = Queue(
    name="analyzer.result",
    exchange=ANALYZER_EXCHANGE,
    routing_key=RESULT_ROUTING_KEY,
    expires=72 * 3600,  # 3 days in seconds
)


class MessageHandler(ConsumerProducerMixin):
    """
    MessageHandler is a consumer that handle messages from RabbitMQ.
    """

    def __init__(self, connection: Connection):
        self.connection = connection

    def get_consumers(self, Consumer, channel):
        return [
            Consumer(queues=[PIPELINE_QUEUE], callbacks=[self.on_pipeline_message]),
            Consumer(queues=[RESULT_QUEUE], callbacks=[self.on_result_message]),
        ]

    def on_pipeline_message(self, _body, message: Message):
        """
        Handle a analyzer pipeline queue message.
        """
        try:
            logger.trace(f"received payload {message.payload}")
            ctx = Context(**message.payload)
        except ValidationError as exception:
            logger.error(exception)
            message.reject()
            return

        try:
            ctx = run_pipeline(ctx)

            self.producer.publish(
                ctx.json(),
                content_type="application/json",
                content_encoding="utf-8",
                exchange=ANALYZER_EXCHANGE,
                routing_key=RESULT_ROUTING_KEY,
                retry=True,
            )

            message.ack()
        except Exception as exception:
            logger.error(exception)
            message.reject()

    def on_result_message(self, _body, message: Message):
        """
        Handle a analyzer result queue message.
        """
        try:
            logger.trace(f"received payload {message.payload}")
            ctx = Context(**message.payload)
        except ValidationError as exception:
            logger.error(exception)
            message.reject()
            return

        try:

            logger.info(f"received context {ctx}")

            report_to_callback(ctx)

            message.ack()
        except Exception as exception:
            logger.error(exception)
            # Only reque on unreachable server, failed request should be rejected
            message.requeue()
