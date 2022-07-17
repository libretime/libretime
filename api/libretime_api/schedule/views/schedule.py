from django.db import models
from django_filters import rest_framework as filters
from rest_framework import viewsets

from ..models import Schedule
from ..serializers import ScheduleSerializer


class ScheduleFilter(filters.FilterSet):
    starts = filters.DateTimeFromToRangeFilter(field_name="starts_at")
    ends = filters.DateTimeFromToRangeFilter(field_name="ends_at")
    position_status = filters.NumberFilter()
    broadcasted = filters.NumberFilter()

    overbooked = filters.BooleanFilter(method="overbooked_filter")

    # pylint: disable=unused-argument
    def overbooked_filter(self, queryset, name, value):
        # TODO: deduplicate code using the overbooked property
        if value:
            return queryset.filter(starts_at__gte=models.F("instance__ends_at"))
        return queryset.filter(starts_at__lt=models.F("instance__ends_at"))

    class Meta:
        model = Schedule
        fields = []  # type: ignore


class ScheduleViewSet(viewsets.ModelViewSet):
    queryset = Schedule.objects.all()
    serializer_class = ScheduleSerializer
    filterset_class = ScheduleFilter
    model_permission_name = "schedule"
