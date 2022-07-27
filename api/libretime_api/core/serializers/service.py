from rest_framework import serializers

from ..models import ServiceRegister


class ServiceRegisterSerializer(serializers.ModelSerializer):
    class Meta:
        model = ServiceRegister
        fields = "__all__"
