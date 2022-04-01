from typing import List, Type

from django.db.models import Model
from django.test.runner import DiscoverRunner


class ManagedModelTestRunner(DiscoverRunner):
    """
    Test runner that automatically makes all unmanaged models in your Django
    project managed for the duration of the test run, so that one doesn't need
    to execute the SQL manually to create them.
    """

    unmanaged_models: List[Type[Model]] = []

    def setup_test_environment(self, *args, **kwargs):
        from django.apps import apps

        for model in apps.get_models():
            if not model._meta.managed:
                model._meta.managed = True
                self.unmanaged_models.append(model)

        super().setup_test_environment(*args, **kwargs)

    def teardown_test_environment(self, *args, **kwargs):
        super().teardown_test_environment(*args, **kwargs)

        # reset unmanaged models
        for model in self.unmanaged_models:
            model._meta.managed = False
