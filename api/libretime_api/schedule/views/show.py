from django_filters import rest_framework as filters
from rest_framework import viewsets

from ..models import Show, ShowDays, ShowHost, ShowInstance, ShowRebroadcast
from ..serializers import (
    ShowDaysSerializer,
    ShowHostSerializer,
    ShowInstanceSerializer,
    ShowRebroadcastSerializer,
    ShowSerializer,
)


class ShowInstanceFilter(filters.FilterSet):
    starts_before = filters.IsoDateTimeFilter(field_name="starts_at", lookup_expr="lte")
    ends_after = filters.IsoDateTimeFilter(field_name="ends_at", lookup_expr="gte")

    class Meta:
        model = ShowInstance
        fields = ["starts_before", "ends_after"]


class ShowViewSet(viewsets.ModelViewSet):
    queryset = Show.objects.all()
    serializer_class = ShowSerializer
    model_permission_name = "show"


class ShowDaysViewSet(viewsets.ModelViewSet):
    queryset = ShowDays.objects.all()
    serializer_class = ShowDaysSerializer
    model_permission_name = "showdays"


class ShowHostViewSet(viewsets.ModelViewSet):
    queryset = ShowHost.objects.all()
    serializer_class = ShowHostSerializer
    model_permission_name = "showhost"


class ShowInstanceViewSet(viewsets.ModelViewSet):
    queryset = ShowInstance.objects.all()
    serializer_class = ShowInstanceSerializer
    model_permission_name = "showinstance"
    filter_backends = [filters.DjangoFilterBackend]
    filterset_class = ShowInstanceFilter


class ShowRebroadcastViewSet(viewsets.ModelViewSet):
    queryset = ShowRebroadcast.objects.all()
    serializer_class = ShowRebroadcastSerializer
    model_permission_name = "showrebroadcast"
