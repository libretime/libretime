from rest_framework import serializers

from ..models import SmartBlock, SmartBlockContent, SmartBlockCriteria


class SmartBlockSerializer(serializers.ModelSerializer):
    class Meta:
        model = SmartBlock
        fields = "__all__"


class SmartBlockContentSerializer(serializers.ModelSerializer):
    class Meta:
        model = SmartBlockContent
        fields = "__all__"


class SmartBlockCriteriaSerializer(serializers.ModelSerializer):
    class Meta:
        model = SmartBlockCriteria
        fields = "__all__"
