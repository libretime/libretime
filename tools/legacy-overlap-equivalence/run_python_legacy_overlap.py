"""The api's answer for every fixture file: "<file id>|true|false".

Prints exactly what run-php.php prints, for the same rows. Neither implementation is
new: both live on main today, and nothing has ever compared them.
"""

import django

django.setup()

from libretime_api.schedule.models import Schedule  # noqa: E402
from libretime_api.storage.models import File  # noqa: E402

ids = list(
    File.objects.filter(id__range=(9000, 9099))
    .order_by("id")
    .values_list("id", flat=True)
)

if not ids:
    raise SystemExit("python: no fixture files, run seed.sql first")

for file_id in ids:
    future = Schedule.is_file_scheduled_in_the_future(file_id)
    print(f"{file_id}|{'true' if future else 'false'}")
