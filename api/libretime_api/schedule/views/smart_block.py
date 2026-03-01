from rest_framework import viewsets

from ..models import SmartBlock, SmartBlockContent, SmartBlockCriteria
from ..serializers import (
    SmartBlockContentSerializer,
    SmartBlockCriteriaSerializer,
    SmartBlockSerializer,
)


class SmartBlockViewSet(viewsets.ModelViewSet):
    queryset = SmartBlock.objects.all()
    serializer_class = SmartBlockSerializer
    model_permission_name = "smartblock"


class SmartBlockContentViewSet(viewsets.ModelViewSet):
    queryset = SmartBlockContent.objects.all()
    serializer_class = SmartBlockContentSerializer
    model_permission_name = "smartblockcontent"


class SmartBlockCriteriaViewSet(viewsets.ModelViewSet):
    queryset = SmartBlockCriteria.objects.all()
    serializer_class = SmartBlockCriteriaSerializer
    model_permission_name = "smartblockcriteria"
