import os

# Make the celeryconfig module visible to celery
os.environ["CELERY_CONFIG_MODULE"] = "libretime_worker.celeryconfig"
