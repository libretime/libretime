class PipelineError(Exception):
    """
    The pipeline failed.

    PipelineError should not occur during a pipeline, and should stop the pipeline.
    """


class StepError(Exception):
    """
    The pipeline step failed.

    StepError may occur during a pipeline, but should not stop the pipeline.
    """
