from django.db import migrations

from ._migrations import legacy_migration_factory

UP = None

DOWN = None


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0008_2_5_10"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.11",
                sql=UP,
            )
        )
    ]


# protected function _runUpgrade()
# {
#     $queryResult = CcFilesQuery::create()
#         ->select(['disk_usage'])
#         ->withColumn('SUM(CcFiles.filesize)', 'disk_usage')
#         ->find();
#     $disk_usage = $queryResult[0];
#     Application_Model_Preference::setDiskUsage($disk_usage);
# }
