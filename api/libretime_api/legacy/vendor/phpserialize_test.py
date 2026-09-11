import unittest
from io import BytesIO

from . import phpserialize


class PhpSerializeTestCase(unittest.TestCase):
    def test_dumps_int(self):
        self.assertEqual(phpserialize.dumps(5), "i:5;")

    def test_dumps_float(self):
        self.assertEqual(phpserialize.dumps(5.6), "d:5.6;")

    def test_dumps_str(self):
        self.assertEqual(phpserialize.dumps("Hello world"), 's:11:"Hello world";')

    def test_dumps_unicode(self):
        self.assertEqual(
            phpserialize.dumps("Björk Guðmundsdóttir"), 's:23:"Björk Guðmundsdóttir";'
        )

    def test_dumps_binary(self):
        self.assertEqual(phpserialize.dumps(b"\001\002\003"), 's:3:"\x01\x02\x03";')

    def test_dumps_list(self):
        self.assertEqual(
            phpserialize.dumps([7, 8, 9]), "a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}"
        )

    def test_dumps_tuple(self):
        self.assertEqual(
            phpserialize.dumps((7, 8, 9)), "a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}"
        )

    def test_dumps_dict(self):
        # print(phpserialize.dumps(OrderedDict({'a': 1, 'b': 2, 'c': 3})))
        self.assertEqual(
            phpserialize.dumps({"a": 1, "b": 2, "c": 3}),
            'a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}',
        )

    def test_loads_dict(self):
        self.assertEqual(
            phpserialize.loads('a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}'),
            {"a": 1, "b": 2, "c": 3},
        )

    def test_loads_unicode(self):
        self.assertEqual(
            phpserialize.loads(b's:23:"Bj\xc3\xb6rk Gu\xc3\xb0mundsd\xc3\xb3ttir";'),
            b"Bj\xc3\xb6rk Gu\xc3\xb0mundsd\xc3\xb3ttir".decode("utf-8"),
        )

    def test_loads_binary(self):
        self.assertEqual(
            phpserialize.loads(b's:3:"\001\002\003";', decode_strings=False),
            b"\001\002\003",
        )

    def test_dumps_and_loads_dict(self):
        self.assertEqual(
            phpserialize.loads(phpserialize.dumps({"a": 1, "b": 2, "c": 3})),
            {"a": 1, "b": 2, "c": 3},
        )

    def test_dumps_and_loads_list(self):
        x = phpserialize.loads(phpserialize.dumps(list(range(2))))
        self.assertEqual(x, [0, 1])

    def test_fileio_support_with_chaining_and_all(self):
        f = BytesIO()
        phpserialize.dump([1, 2], f)
        phpserialize.dump(42, f)
        f = BytesIO(f.getvalue())
        self.assertEqual(phpserialize.load(f), [1, 2])
        self.assertEqual(phpserialize.load(f), 42)

    def test_object_hook(self):
        class User:
            def __init__(self, username):
                self.username = username

        def load_object_hook(name, d):
            return {"WP_User": User}[name](**d)

        def dump_object_hook(obj):
            if isinstance(obj, User):
                return phpserialize.phpobject("WP_User", {"username": obj.username})
            raise LookupError("unknown object")

        user = User("test")
        x = phpserialize.dumps(user, object_hook=dump_object_hook)
        y = phpserialize.loads(x, object_hook=load_object_hook, decode_strings=True)
        self.assertTrue("WP_User" in x)
        self.assertEqual(type(y), type(user))
        self.assertEqual(y.username, user.username)

    def test_basic_object_hook(self):
        data = b'O:7:"WP_User":1:{s:8:"username";s:5:"admin";}'
        user = phpserialize.loads(
            data, object_hook=phpserialize.phpobject, decode_strings=True
        )
        self.assertEqual(user.username, "admin")
        self.assertEqual(user.__name__, "WP_User")
