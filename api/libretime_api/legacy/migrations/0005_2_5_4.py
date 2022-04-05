from django.db import migrations

from ._migrations import legacy_migration_factory

UP = None

DOWN = None


def legacy_migration(cursor):
    # //First, ensure there are no superadmins already.
    # $numberOfSuperAdmins = CcSubjsQuery::create()
    #     ->filterByDbType(UTYPE_SUPERADMIN)
    #     ->filterByDbLogin('sourcefabric_admin', Criteria::NOT_EQUAL) //Ignore sourcefabric_admin users
    #     ->count();

    # //Only create a super admin if there isn't one already.
    # if ($numberOfSuperAdmins == 0) {
    #     //Find the "admin" user and promote them to superadmin.
    #     $adminUser = CcSubjsQuery::create()
    #         ->filterByDbLogin('admin')
    #         ->findOne();
    #     if (!$adminUser) {
    #         // Otherwise get the user with the lowest ID that is of type administrator:
    #         $adminUser = CcSubjsQuery::create()
    #             ->filterByDbType(UTYPE_ADMIN)
    #             ->orderByDbId(Criteria::ASC)
    #             ->findOne();

    #         if (!$adminUser) {
    #             throw new Exception("Failed to find any users of type 'admin' ('A').");
    #         }
    #     }

    #     $adminUser = new Application_Model_User($adminUser->getDbId());
    #     $adminUser->setType(UTYPE_SUPERADMIN);
    #     $adminUser->save();
    #     Logging::info($_SERVER['HTTP_HOST'] . ': ' . $this->getNewVersion() . ' Upgrade: Promoted user ' . $adminUser->getLogin() . ' to be a Super Admin.');

    #     //Also try to promote the sourcefabric_admin user
    #     $sofabAdminUser = CcSubjsQuery::create()
    #         ->filterByDbLogin('sourcefabric_admin')
    #         ->findOne();
    #     if ($sofabAdminUser) {
    #         $sofabAdminUser = new Application_Model_User($sofabAdminUser->getDbId());
    #         $sofabAdminUser->setType(UTYPE_SUPERADMIN);
    #         $sofabAdminUser->save();
    #         Logging::info($_SERVER['HTTP_HOST'] . ': ' . $this->getNewVersion() . ' Upgrade: Promoted user ' . $sofabAdminUser->getLogin() . ' to be a Super Admin.');
    #     }
    # }
    pass


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0004_2_5_3"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.4",
                before=legacy_migration,
            )
        )
    ]
