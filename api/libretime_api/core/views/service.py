from rest_framework import viewsets

from ..models import ServiceRegister
from ..serializers import ServiceRegisterSerializer


class ServiceRegisterViewSet(viewsets.ModelViewSet):
    queryset = ServiceRegister.objects.all()
    serializer_class = ServiceRegisterSerializer
    model_permission_name = "serviceregister"
