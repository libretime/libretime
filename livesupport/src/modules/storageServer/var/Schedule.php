<?php
class Schedule 
{
     function Schedule() {}

    /**
     *  Open schedule import
     *
     *  @param filename :  string    - import file
     *  @return status  :  hasharray - fields:
     *                          token:  string - schedule import token
     */
     function openImport($sessid,$filename) {
        return array ('token' => 'abcdef0123456789');
     }

    /**
     *  Check status of schedule import
     *
     *  @param token   :  string    -  schedule import token
     *  @return status :  hasharray - fields:
     *                          token:  string - schedule import token
     *                          status: string - working | fault | success
     */
     function checkImport($token) {
        if ($token == 'abcdef0123456789') {
            return array(
                'token'     => $token,
                'status'    => 'working'
            );
        } else {
            return PEAR::raiseError('Schedule::checkImport: invalid token');
        }
     }
    
    /**
     *  Close schedule import
     *
     *  @param token   :  string    -  schedule import token
     *  @return status :  hasharray - fields:
     *                          token:  string - schedule import token
     */
     function closeImport($token) {
        if ($token == 'abcdef0123456789') {
            return array(
                'token'     => $token
            );
        } else {
            return PEAR::raiseError('Schedule::closeImport: invalid token');
        }
     }

    /**
     *  Open schedule export
     *
     *  @param sessid   :  string    - schedule import token
     *  @param criteria :  struct    - see search criteria
     *  @param fromTime :  time      - begining time of schedule export 
     *  @param toTime   :  time      - ending time of schedule export
     *  @return status  : hasharray - fields:
     *                          token:  string - schedule export token
     */
     function openExport($sessid,$criteria,$fromTime,$toTime) {
        return array ('token' => '123456789abcdef0');
     }
     
    /**
     *  Check status of schedule export
     *
     *  @param token   :  string    -  schedule export token
     *  @return status :  hasharray - fields:
     *                          token:  string - schedule export token
     *                          status: string - working | fault | success
     *                          file :  string - exported file location (available if status is success)
     */
     function checkExport($token) {
        if ($token == '123456789abcdef0') {
            return array(
                'token'     => $token,
                'status'    => 'working'
            );
        } else {
            PEAR::raiseError('Schedule::checkExport: invalid token');
        }
     }
     
    /**
     *  Close schedule export
     *
     *  @param token   :  string    -  schedule export token
     *  @return status :  hasharray - fields:
     *                          token:  string - schedule export token
     */
     function closeExport($token) {
        if ($token == '123456789abcdef0') {
            return array(
                'token'     => $token
            );
        } else {
            PEAR::raiseError('Schedule::closeExport: invalid token');
        }
     }
}
?>
