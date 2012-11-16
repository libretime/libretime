# -*- coding: utf-8 -*-
import re
from media.saas.launcher import setup_logger, setup_global, MM2
from media.saas.airtimeinstance import AirtimeInstance
from os.path import isdir, join, abspath
from os import listdir

def list_dirs(d): return (x for x in listdir(d) if isdir(x))

def filter_instance(d): return bool(re.match('.+/\d+$',d))

def get_name(p): return re.match('.+/(\d+)$',p).group(1)

def filter_instances(l): return (x for x in l if filter_instance(l))

def autoscan_instances(main_cfg):
    root = main_cfg['instance_root']
    instances = []
    for instance_machine in list_dirs(root):
        instance_machine = join(root, instance_machine)
        for instance_root in filter_instances(list_dirs(instance_machine)):
            full_path = abspath(join(root,instance_root))
            ai = AirtimeInstance.root_make(get_name(full_path), full_path)
            instances.append(ai)
    return instances

def main(main_cfg):
    log_config, log_path = main_cfg['log_config'], main_cfg['log_path']
    log = setup_logger(log_config, log_path)
    setup_global(log)
    for instance in autoscan_instances(main_cfg): MM2(instance).start()

if __name__ == '__main__': 
    default = {
            'log_config'    : '',
            'log_path'      : '',
            'instance_root' : ''
    }
    main(default)
