from django.conf import settings
from rest_framework.permissions import AllowAny
from rest_framework.response import Response
from rest_framework.views import APIView

from ..serializers import VersionSerializer


class VersionView(APIView):
    permission_classes = [AllowAny]
    serializer_class = VersionSerializer

    def get(self, request):
        return Response({"api_version": settings.API_VERSION})
