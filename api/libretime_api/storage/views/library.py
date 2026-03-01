from rest_framework import viewsets

from ..models import Library
from ..serializers import LibrarySerializer


class LibraryViewSet(viewsets.ModelViewSet):
    queryset = Library.objects.all()
    serializer_class = LibrarySerializer
    model_permission_name = "library"
