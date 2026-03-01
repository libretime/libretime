from rest_framework import viewsets

from ..models import Preference
from ..serializers import PreferenceSerializer


class PreferenceViewSet(viewsets.ModelViewSet):
    queryset = Preference.objects.all()
    serializer_class = PreferenceSerializer
    model_permission_name = "preference"
