from rest_framework import viewsets

from ..models import (
    PlayoutHistory,
    PlayoutHistoryMetadata,
    PlayoutHistoryTemplate,
    PlayoutHistoryTemplateField,
)
from ..serializers import (
    PlayoutHistoryMetadataSerializer,
    PlayoutHistorySerializer,
    PlayoutHistoryTemplateFieldSerializer,
    PlayoutHistoryTemplateSerializer,
)


class PlayoutHistoryViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistory.objects.all()
    serializer_class = PlayoutHistorySerializer
    model_permission_name = "playouthistory"


class PlayoutHistoryMetadataViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryMetadata.objects.all()
    serializer_class = PlayoutHistoryMetadataSerializer
    model_permission_name = "playouthistorymetadata"


class PlayoutHistoryTemplateViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryTemplate.objects.all()
    serializer_class = PlayoutHistoryTemplateSerializer
    model_permission_name = "playouthistorytemplate"


class PlayoutHistoryTemplateFieldViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryTemplateField.objects.all()
    serializer_class = PlayoutHistoryTemplateFieldSerializer
    model_permission_name = "playouthistorytemplatefield"
