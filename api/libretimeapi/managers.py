from django.contrib.auth.models import BaseUserManager

class UserManager(BaseUserManager):
    def create_user(self, username, type, email, first_name, last_name, password):
        user = self.model(username=username,
                          type=type,
                          email=email,
                          first_name=first_name,
                          last_name=last_name)
        user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, username, email, first_name, last_name, password):
        user = self.create_user(username, 'A', email, first_name, last_name, password)
        return user

    def get_by_natural_key(self, username):
        return self.get(username=username)

