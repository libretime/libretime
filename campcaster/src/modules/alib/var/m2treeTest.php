<?php
require_once("M2tree.php");

//class M2treeTest extends M2tree {
class M2treeTest {
    
    function _test_init()
    {
        for ($i = 1; $i <= 3; $i++) {
            $r = M2tree::AddObj("Publication$i", "Publication");
            if (PEAR::isError($r)) {
                return $r;
            }
            $this->_t["p$i"] = $r;
        }
        for ($i = 1; $i <= 3; $i++){
            $r = M2tree::AddObj("Issue$i", "Issue", $this->_t[$i<=2 ? 'p1' : 'p2']);
            if (PEAR::isError($r)) {
                return $r;
            }
            $this->_t["i$i"] = $r;
        }
        for ($i = 1; $i <= 4; $i++){
            $r = M2tree::AddObj("Section$i", "Section", $this->_t[$i<=3 ? 'i1' : 'i3']);
            if (PEAR::isError($r)) {
                return $r;
            }
            $this->_t["s$i"] = $r;
        }
        $r = M2tree::AddObj("Par1", "Par", $this->_t["s2"]);
        if (PEAR::isError($r)) {
            return $r;
        }
        $this->_t["r1"] = $r;
    }
    
    
    function _test_check($title, $expected, $returned)
    {
        global $CC_DBC;
        if ($expected !== $returned){
            return $CC_DBC->raiseError(
                "m2tree::$title FAILED:\n".
                " ###expected:\n$expected\n ---\n".
                " ###returned:\n$returned\n ---\n"
            );
        }
        return "#  ".get_class($this)."::$title: OK\n";
    }
    
    
    function _test()
    {
        echo "# M2tree test:\n";

        // addObj/dumpTree test:
        $r = $this->_test_init();
        if (PEAR::isError($r)) {
            return $r;
        }
        $expected = "RootNode
    Publication1
        Issue1
            Section1
            Section2
                Par1
            Section3
        Issue2
    Publication2
        Issue3
            Section4
    Publication3
";
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        if (PEAR::isError($returned)) {
            return $returned;
        }
        $r = $this->_test_check('addObj/dumpTree', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // shaking test:
        $nid = M2tree::CopyObj($this->_t['s2'], $this->_t['s4']);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        $r = M2tree::RemoveObj($this->_t['s2']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = M2tree::MoveObj($nid, $this->_t['i1']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        if (PEAR::isError($returned)) {
            return $returned;
        }
        $r = $this->_test_check('shaking test', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // removeObj test:
        $r = M2tree::RemoveObj($this->_t['p2']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $expected = "RootNode
    Publication1
        Issue1
            Section1
            Section2
                Par1
            Section3
        Issue2
    Publication3
";
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('removeObj', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // renameObj/getObjName test:
        $original = M2tree::GetObjName($this->_t['i2']);
        if (PEAR::isError($original)) {
            return $original;
        }
        $changed = 'Issue2_changed';
        $expected = $original.$changed;
        $r = M2tree::RenameObj($this->_t['i2'], $changed);
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = M2tree::GetObjName($this->_t['i2']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $returned = $r;
        $r = M2tree::RenameObj($this->_t['i2'], $original);
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = M2tree::GetObjName($this->_t['i2']);
        $returned = $r.$returned;
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = $this->_test_check('renameObj/getObjName', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // getPath test:
        $expected = "RootNode, Publication1, Issue1, Section3";
        $r = M2tree::GetPath($this->_t['s3'], 'name');
        $returned = join(', ', array_map(create_function('$it', 'return $it["name"];'), $r));
        $r = $this->_test_check('getPath', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // getObjType test:
        $expected = 'Issue';
        $returned = M2tree::GetObjType($this->_t['i2']);
        $r = $this->_test_check('getObjType', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // getParent test:
        $expected = $this->_t['p1'];
        $returned = M2tree::GetParent($this->_t['i2']);
        $r = $this->_test_check('getParent', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r;
        } else {
            echo $r;
        }

        // getDir test:
        $expected = "Issue1, Issue2";
        $r = M2tree::GetDir($this->_t['p1'], 'name');
        $returned = join(', ', array_map(create_function('$it', 'return $it["name"];'), $r));
        $r = $this->_test_check('getDir', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r;
        } else {
            echo $r;
        }

        // getObjId test:
        $expected = $this->_t['i2'];
        $returned = M2tree::GetObjId('Issue2', $this->_t['p1']);
        $r = $this->_test_check('getObjId', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r;
        } else {
            echo $r;
        }

        // getObjLevel test:
        $expected = 2;
        $r = M2tree::GetObjLevel($this->_t['i2']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $returned = $r['level'];
        $r = $this->_test_check('getObjLevel', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }

        // copyObj test:
        $expected = "RootNode
    Publication1
        Issue1
            Section1
            Section2
                Par1
            Section3
        Issue2
    Publication3
        Issue1
            Section1
            Section2
                Par1
            Section3
";
        $nid = M2tree::CopyObj($this->_t['i1'], $this->_t['p3']);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('copyObj', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r; 
        } else {
            echo $r;
        }
        M2tree::RemoveObj($nid);

        // moveObj test:
        $expected = "RootNode
    Publication1
        Issue2
    Publication3
        Issue1
            Section1
            Section2
                Par1
            Section3
";
        $r = M2tree::MoveObj($this->_t['i1'], $this->_t['p3']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('moveObj', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r;
        } else {
            echo $r;
        }

        // _cutSubtree test:
        // _pasteSubtree test:

        echo M2tree::DumpTree();

        // reset test:
        $expected = "RootNode\n";
        $r = M2tree::reset();
        if (PEAR::isError($r)) {
            return $r;
        }
        $returned = M2tree::DumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('reset', $expected, $returned);
        if (PEAR::isError($r)) {
            return $r;
        } else {
            echo $r;
        }

        echo "# M2tree OK\n";
        return TRUE;
    }

}
?>