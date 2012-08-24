# -*- coding: utf-8 -*-
import unittest
import media.monitor.owners as owners

class TestMMP(unittest.TestCase):
    def setUp(self):
        self.f = "test.mp3"

    def test_has_owner(self):
        owners.reset_owners()
        o = 12345
        self.assertTrue( owners.add_file_owner(self.f,o) )
        self.assertTrue( owners.has_owner(self.f) )

    def test_add_file_owner(self):
        owners.reset_owners()
        self.assertFalse( owners.add_file_owner('testing', -1) )
        self.assertTrue( owners.add_file_owner(self.f, 123) )
        self.assertTrue( owners.add_file_owner(self.f, 123) )
        self.assertTrue( owners.add_file_owner(self.f, 456) )

    def test_remove_file_owner(self):
        owners.reset_owners()
        self.assertTrue( owners.add_file_owner(self.f, 123) )
        self.assertTrue( owners.remove_file_owner(self.f) )
        self.assertFalse( owners.remove_file_owner(self.f) )

if __name__ == '__main__': unittest.main()

