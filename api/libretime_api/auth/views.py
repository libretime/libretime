from dj_rest_auth.views import LoginView as BaseLoginView, LogoutView as BaseLogoutView


class LoginView(BaseLoginView):
    def post(self, request, *args, **kwargs):
        response = super().post(request, *args, **kwargs)

        # TODO: Create legacy session

        return response


class LogoutView(BaseLogoutView):
    # Fix schema generation
    serializer_class = None

    def post(self, request, *args, **kwargs):
        response = super().post(request, *args, **kwargs)

        # TODO: Delete legacy session

        return response
