from rest_framework import serializers

from ..models import ServiceRegister


class ServiceRegisterSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ServiceRegister
        fields = "__all__"
