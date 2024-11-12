class ReadWriteSerializerMixin:
    """
    Overrides get_serializer_class to choose the read serializer
    for GET requests and the write serializer for POST requests.

    Set read_serializer_class and write_serializer_class attributes on a
    viewset.
    """

    read_serializer_class = None
    write_serializer_class = None

    def get_serializer_class(self):
        if self.action in ["create"]:
            return self.get_write_serializer_class()
        return self.get_read_serializer_class()

    def get_read_serializer_class(self):
        assert self.read_serializer_class is not None, (
            f"'{self.__class__.__name__}' should either include a `read_serializer_class` attribute,"
            "or override the `get_read_serializer_class()` method."
        )
        return self.read_serializer_class

    def get_write_serializer_class(self):
        assert self.write_serializer_class is not None, (
            f"'{self.__class__.__name__}' should either include a `write_serializer_class` attribute,"
            "or override the `get_write_serializer_class()` method."
        )
        return self.write_serializer_class
