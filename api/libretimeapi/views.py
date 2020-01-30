import os
from django.conf import settings
from django.http import FileResponse
from django.shortcuts import get_object_or_404
from rest_framework import status, viewsets
from rest_framework.decorators import api_view, action, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response
from .serializers import *
from .permissions import IsAdminOrOwnUser

class UserViewSet(viewsets.ModelViewSet):
    queryset = get_user_model().objects.all()
    serializer_class = UserSerializer
    permission_classes = [IsAdminOrOwnUser]
    model_permission_name = 'user'

class SmartBlockViewSet(viewsets.ModelViewSet):
    queryset = SmartBlock.objects.all()
    serializer_class = SmartBlockSerializer
    model_permission_name = 'smartblock'

class SmartBlockContentViewSet(viewsets.ModelViewSet):
    queryset = SmartBlockContent.objects.all()
    serializer_class = SmartBlockContentSerializer
    model_permission_name = 'smartblockcontent'

class SmartBlockCriteriaViewSet(viewsets.ModelViewSet):
    queryset = SmartBlockCriteria.objects.all()
    serializer_class = SmartBlockCriteriaSerializer
    model_permission_name = 'smartblockcriteria'

class CountryViewSet(viewsets.ModelViewSet):
    queryset = Country.objects.all()
    serializer_class = CountrySerializer
    model_permission_name = 'country'

class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = 'file'

    @action(detail=True, methods=['GET'])
    def download(self, request, pk=None):
        if pk is None:
            return Response('No file requested', status=status.HTTP_400_BAD_REQUEST)
        try:
            pk = int(pk)
        except ValueError:
            return Response('File ID should be an integer',
                            status=status.HTTP_400_BAD_REQUEST)

        filename = get_object_or_404(File, pk=pk)
        directory = filename.directory
        path = os.path.join(directory.directory, filename.filepath)
        response = FileResponse(open(path, 'rb'), content_type=filename.mime)
        return response

class ListenerCountViewSet(viewsets.ModelViewSet):
    queryset = ListenerCount.objects.all()
    serializer_class = ListenerCountSerializer
    model_permission_name = 'listenercount'

class LiveLogViewSet(viewsets.ModelViewSet):
    queryset = LiveLog.objects.all()
    serializer_class = LiveLogSerializer
    model_permission_name = 'livelog'

class LoginAttemptViewSet(viewsets.ModelViewSet):
    queryset = LoginAttempt.objects.all()
    serializer_class = LoginAttemptSerializer
    model_permission_name = 'loginattempt'

class MountNameViewSet(viewsets.ModelViewSet):
    queryset = MountName.objects.all()
    serializer_class = MountNameSerializer
    model_permission_name = 'mountname'

class MusicDirViewSet(viewsets.ModelViewSet):
    queryset = MusicDir.objects.all()
    serializer_class = MusicDirSerializer
    model_permission_name = 'musicdir'

class PlaylistViewSet(viewsets.ModelViewSet):
    queryset = Playlist.objects.all()
    serializer_class = PlaylistSerializer
    model_permission_name = 'playlist'

class PlaylistContentViewSet(viewsets.ModelViewSet):
    queryset = PlaylistContent.objects.all()
    serializer_class = PlaylistContentSerializer
    model_permission_name = 'playlistcontent'

class PlayoutHistoryViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistory.objects.all()
    serializer_class = PlayoutHistorySerializer
    model_permission_name = 'playouthistory'

class PlayoutHistoryMetadataViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryMetadata.objects.all()
    serializer_class = PlayoutHistoryMetadataSerializer
    model_permission_name = 'playouthistorymetadata'

class PlayoutHistoryTemplateViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryTemplate.objects.all()
    serializer_class = PlayoutHistoryTemplateSerializer
    model_permission_name = 'playouthistorytemplate'

class PlayoutHistoryTemplateFieldViewSet(viewsets.ModelViewSet):
    queryset = PlayoutHistoryTemplateField.objects.all()
    serializer_class = PlayoutHistoryTemplateFieldSerializer
    model_permission_name = 'playouthistorytemplatefield'

class PreferenceViewSet(viewsets.ModelViewSet):
    queryset = Preference.objects.all()
    serializer_class = PreferenceSerializer
    model_permission_name = 'perference'

class ScheduleViewSet(viewsets.ModelViewSet):
    queryset = Schedule.objects.all()
    serializer_class = ScheduleSerializer
    filter_fields = ('starts', 'ends', 'playout_status', 'broadcasted')
    model_permission_name = 'schedule'

class ServiceRegisterViewSet(viewsets.ModelViewSet):
    queryset = ServiceRegister.objects.all()
    serializer_class = ServiceRegisterSerializer
    model_permission_name = 'serviceregister'

class SessionViewSet(viewsets.ModelViewSet):
    queryset = Session.objects.all()
    serializer_class = SessionSerializer
    model_permission_name = 'session'

class ShowViewSet(viewsets.ModelViewSet):
    queryset = Show.objects.all()
    serializer_class = ShowSerializer
    model_permission_name = 'show'

class ShowDaysViewSet(viewsets.ModelViewSet):
    queryset = ShowDays.objects.all()
    serializer_class = ShowDaysSerializer
    model_permission_name = 'showdays'

class ShowHostViewSet(viewsets.ModelViewSet):
    queryset = ShowHost.objects.all()
    serializer_class = ShowHostSerializer
    model_permission_name = 'showhost'

class ShowInstanceViewSet(viewsets.ModelViewSet):
    queryset = ShowInstance.objects.all()
    serializer_class = ShowInstanceSerializer
    model_permission_name = 'showinstance'

class ShowRebroadcastViewSet(viewsets.ModelViewSet):
    queryset = ShowRebroadcast.objects.all()
    serializer_class = ShowRebroadcastSerializer
    model_permission_name = 'showrebroadcast'

class StreamSettingViewSet(viewsets.ModelViewSet):
    queryset = StreamSetting.objects.all()
    serializer_class = StreamSettingSerializer
    model_permission_name = 'streamsetting'

class UserTokenViewSet(viewsets.ModelViewSet):
    queryset = UserToken.objects.all()
    serializer_class = UserTokenSerializer
    model_permission_name = 'usertoken'

class TimestampViewSet(viewsets.ModelViewSet):
    queryset = Timestamp.objects.all()
    serializer_class = TimestampSerializer
    model_permission_name = 'timestamp'

class WebstreamViewSet(viewsets.ModelViewSet):
    queryset = Webstream.objects.all()
    serializer_class = WebstreamSerializer
    model_permission_name = 'webstream'

class WebstreamMetadataViewSet(viewsets.ModelViewSet):
    queryset = WebstreamMetadata.objects.all()
    serializer_class = WebstreamMetadataSerializer
    model_permission_name = 'webstreametadata'

class CeleryTaskViewSet(viewsets.ModelViewSet):
    queryset = CeleryTask.objects.all()
    serializer_class = CeleryTaskSerializer
    model_permission_name = 'celerytask'

class CloudFileViewSet(viewsets.ModelViewSet):
    queryset = CloudFile.objects.all()
    serializer_class = CloudFileSerializer
    model_permission_name = 'cloudfile'

class ImportedPodcastViewSet(viewsets.ModelViewSet):
    queryset = ImportedPodcast.objects.all()
    serializer_class = ImportedPodcastSerializer
    model_permission_name = 'importedpodcast'

class PodcastViewSet(viewsets.ModelViewSet):
    queryset = Podcast.objects.all()
    serializer_class = PodcastSerializer
    model_permission_name = 'podcast'

class PodcastEpisodeViewSet(viewsets.ModelViewSet):
    queryset = PodcastEpisode.objects.all()
    serializer_class = PodcastEpisodeSerializer
    model_permission_name = 'podcastepisode'

class StationPodcastViewSet(viewsets.ModelViewSet):
    queryset = StationPodcast.objects.all()
    serializer_class = StationPodcastSerializer
    model_permission_name = 'station'

class ThirdPartyTrackReferenceViewSet(viewsets.ModelViewSet):
    queryset = ThirdPartyTrackReference.objects.all()
    serializer_class = ThirdPartyTrackReferenceSerializer
    model_permission_name = 'thirdpartytrackreference'

class TrackTypeViewSet(viewsets.ModelViewSet):
    queryset = TrackType.objects.all()
    serializer_class = TrackTypeSerializer
    model_permission_name = 'tracktype'

@api_view(['GET'])
@permission_classes((AllowAny, ))
def version(request, *args, **kwargs):
    return Response({'api_version': settings.API_VERSION})
