from rest_framework import serializers

from ..models import SmartBlock, SmartBlockContent, SmartBlockCriteria


class SmartBlockSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = SmartBlock
        fields = "__all__"


class SmartBlockContentSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = SmartBlockContent
        fields = "__all__"


class SmartBlockCriteriaSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = SmartBlockCriteria
        fields = "__all__"
