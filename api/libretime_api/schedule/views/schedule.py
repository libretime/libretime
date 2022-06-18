from django.db.models import F
from drf_spectacular.utils import OpenApiParameter, extend_schema, extend_schema_view
from rest_framework import viewsets

from ..._constants import FILTER_NUMERICAL_LOOKUPS
from ..models import Schedule
from ..serializers import ScheduleSerializer


@extend_schema_view(
    list=extend_schema(
        parameters=[
            OpenApiParameter(
                name="is_valid",
                description="Filter on valid instances",
                required=False,
                type=bool,
            ),
        ]
    )
)
class ScheduleViewSet(viewsets.ModelViewSet):
    queryset = Schedule.objects.all()
    serializer_class = ScheduleSerializer
    filterset_fields = {
        "starts": FILTER_NUMERICAL_LOOKUPS,
        "ends": FILTER_NUMERICAL_LOOKUPS,
        "playout_status": FILTER_NUMERICAL_LOOKUPS,
        "broadcasted": FILTER_NUMERICAL_LOOKUPS,
    }
    model_permission_name = "schedule"

    def get_queryset(self):
        filter_valid = self.request.query_params.get("is_valid")
        if filter_valid is None:
            return self.queryset.all()

        filter_valid = filter_valid.strip().lower() in ("true", "yes", "1")
        if filter_valid:
            return self.queryset.filter(starts__lt=F("instance__ends"))

        return self.queryset.filter(starts__gte=F("instance__ends"))
