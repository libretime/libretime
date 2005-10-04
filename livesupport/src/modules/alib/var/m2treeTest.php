<?php
require_once"m2tree.php";

class M2treeTest extends M2tree{
    function _test_init()
    {
        for($i=1; $i<=3; $i++){
            $r = $this->addObj("Publication$i", "Publication");
            if($this->dbc->isError($r)) return $r;
            $this->_t["p$i"] = $r;
        }
        for($i=1; $i<=3; $i++){
            $r = $this->addObj("Issue$i", "Issue",
                $this->_t[$i<=2 ? 'p1' : 'p2']);
            if($this->dbc->isError($r)) return $r;
            $this->_t["i$i"] = $r;
        }
        for($i=1; $i<=4; $i++){
            $r = $this->addObj("Section$i", "Section",
                $this->_t[$i<=3 ? 'i1' : 'i3']);
            if($this->dbc->isError($r)) return $r;
            $this->_t["s$i"] = $r;
        }
        $r = $this->addObj("Par1", "Par", $this->_t["s2"]);
        if($this->dbc->isError($r)) return $r;
        $this->_t["r1"] = $r;
    }
    function _test_check($title, $expected, $returned)
    {
        if($expected !== $returned){
            return $this->dbc->raiseError(
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
        if($this->dbc->isError($r)) return $r;
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
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        if($this->dbc->isError($returned)) return $returned;
        $r = $this->_test_check('addObj/dumpTree', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // shaking test:
        $nid = $this->copyObj($this->_t['s2'], $this->_t['s4']);
        if($this->dbc->isError($nid)) return $nid;
        $r = $this->removeObj($this->_t['s2']);
        if($this->dbc->isError($r)) return $r;
        $r = $this->moveObj($nid, $this->_t['i1']);
        if($this->dbc->isError($r)) return $r;
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        if($this->dbc->isError($returned)) return $returned;
        $r = $this->_test_check('shaking test', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;


        // removeObj test:
        $r = $this->removeObj($this->_t['p2']);
        if($this->dbc->isError($r)) return $r;
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
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('removeObj', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // renameObj/getObjName test:
        $original = $this->getObjName($this->_t['i2']);
        if($this->dbc->isError($original)) return $original;
        $changed = 'Issue2_changed';
        $expected = $original.$changed;
        $r = $this->renameObj($this->_t['i2'], $changed);
        if($this->dbc->isError($r)) return $r;
        $r = $this->getObjName($this->_t['i2']);
        if($this->dbc->isError($r)) return $r;
        $returned = $r;
        $r = $this->renameObj($this->_t['i2'], $original);
        if($this->dbc->isError($r)) return $r;
        $r = $this->getObjName($this->_t['i2']);
        $returned = $r.$returned;
        if($this->dbc->isError($r)) return $r;
        $r = $this->_test_check('renameObj/getObjName', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getPath test:
        $expected = "RootNode, Publication1, Issue1, Section3";
        $r = $this->getPath($this->_t['s3'], 'name');
        $returned = join(', ', array_map(create_function('$it', 'return $it["name"];'), $r));
        $r = $this->_test_check('getPath', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getObjType test:
        $expected = 'Issue';
        $returned = $this->getObjType($this->_t['i2']);
        $r = $this->_test_check('getObjType', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getParent test:
        $expected = $this->_t['p1'];
        $returned = $this->getParent($this->_t['i2']);
        $r = $this->_test_check('getParent', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getDir test:
        $expected = "Issue1, Issue2";
        $r = $this->getDir($this->_t['p1'], 'name');
        $returned = join(', ', array_map(create_function('$it', 'return $it["name"];'), $r));
        $r = $this->_test_check('getDir', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getObjId test:
        $expected = $this->_t['i2'];
        $returned = $this->getObjId('Issue2', $this->_t['p1']);
        $r = $this->_test_check('getObjId', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // getObjLevel test:
        $expected = 2;
        $r = $this->getObjLevel($this->_t['i2']);
        if($this->dbc->isError($r)) return $r;
        $returned = $r['level'];
        $r = $this->_test_check('getObjLevel', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

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
        $nid = $this->copyObj($this->_t['i1'], $this->_t['p3']);
        if($this->dbc->isError($nid)) return $nid;
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('copyObj', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;
        $this->removeObj($nid);

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
        $r = $this->moveObj($this->_t['i1'], $this->_t['p3']);
        if($this->dbc->isError($r)) return $r;
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('moveObj', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        // _cutSubtree test:
        // _pasteSubtree test:

        echo $this->dumpTree();

        // reset test:
        $expected = "RootNode\n";
        $r = $this->reset();
        if($this->dbc->isError($r)) return $r;
        $returned = $this->dumpTree(NULL, '    ', '', '{name}');
        $r = $this->_test_check('reset', $expected, $returned);
        if($this->dbc->isError($r)) return $r; else echo $r;

        echo "# M2tree OK\n";
        return TRUE;
    }

}
?>