from rest_framework import viewsets

from ..models import Country
from ..serializers import CountrySerializer


class CountryViewSet(viewsets.ModelViewSet):
    queryset = Country.objects.all()
    serializer_class = CountrySerializer
    model_permission_name = "country"
