# -*- coding: utf-8 -*-
import re
from media.saas.launcher import setup_logger, setup_global, MM2
from media.saas.airtimeinstance import AirtimeInstance
from os.path import isdir, join, abspath, exists, dirname
from os import listdir

def list_dirs(d): return (x for x in listdir(d) if isdir(join(d,x)))

def filter_instance(d): return bool(re.match('.+\d+$',d))

def get_name(p): return re.match('.+/(\d+)$',p).group(1)

def filter_instances(l): return (x for x in l if filter_instance(x))

def autoscan_instances(main_cfg):
    root = main_cfg['instance_root']
    instances = []
    for instance_machine in list_dirs(root):
        instance_machine = join(root, instance_machine)
        for instance_root in filter_instances(list_dirs(instance_machine)):
            full_path = abspath(join(instance_machine,instance_root))
            ai = AirtimeInstance.root_make(get_name(full_path), full_path)
            instances.append(ai)
    return instances

def verify_exists(p):
    if not exists(p): raise Exception("%s must exist" % p)

def main(main_cfg):
    log_config, log_path = main_cfg['log_config'], main_cfg['log_path']
    verify_exists(log_config)
    log = setup_logger(log_config, log_path)
    setup_global(log)
    for instance in autoscan_instances(main_cfg):
        print("Launching instance: %s" % str(instance))
        #MM2(instance).start()
    print("Launched all instances")

if __name__ == '__main__':
    pwd = dirname(__file__)
    default = {
            'log_path'      : join(pwd, 'test.log'), # config for log
            'log_config'    : join(pwd, 'configs/logging.cfg'), # where to log
            # root dir of all instances
            'instance_root' : '/mnt/airtimepro/instances'
    }
    main(default)
