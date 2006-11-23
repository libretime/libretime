<?php

// needed for locking test
define('FILE_LOCKS_BLOCK', false, true);

require_once 'PHPUnit.php';
require_once 'File.php';

class FileTest extends PHPUnit_TestCase 
{
    function FileTest($name = 'FileTest')
    {
        $this->PHPUnit_TestCase($name);
    }

    function getTestString()
    {
        static $str;
        isset($str) or $str = str_repeat(str_repeat("0123456789", 1000)."\n", 100);
        return $str;
    }
    
    function getTestLine()
    {
        static $str;
        isset($str) or $str = str_repeat("0123456789", 1000);
        return $str;
    }
    
    function setUp()
    {
        $this->tearDown();
        if (PEAR::isError($e = File::write('test.txt', $this->getTestString(), FILE_MODE_WRITE))) {
            die("Cannot start test: ". str_replace($this->getTestString(),'...', $e->getMessage()));
        }
    }

    function tearDown()
    {
        File::closeAll();
        file_exists('test.txt') and unlink('test.txt');
    }

    function testlocking()
    {
        $this->assertFalse(PEAR::isError(File::write('test.txt', 'abc', FILE_MODE_APPEND, true)));
        $this->assertTrue(PEAR::isError(File::write('test.txt', 'def', FILE_MODE_WRITE, true)));
        $this->assertFalse(PEAR::isError(File::unlock('test.txt', FILE_MODE_APPEND)));
        $this->assertFalse(PEAR::isError(File::unlock('test.txt', FILE_MODE_WRITE)));
    }

    function testclose()
    {
        $this->assertFalse(PEAR::isError(File::close('test.txt', FILE_MODE_WRITE)));
        $this->assertFalse(PEAR::isError(File::close('test.txt', FILE_MODE_APPEND)));
        $this->assertFalse(PEAR::isError(File::close('test.txt', FILE_MODE_READ)));
    }

    function testreadAll()
    {
        $this->assertEquals($this->getTestString(), File::readAll('test.txt'));
        $this->assertEquals($this->getTestString(), File::readAll('test.txt'));
        $this->assertEquals($this->getTestString(), File::readAll('test.txt'));
    }

    function testread()
    {
        $this->assertEquals($this->getTestLine(), File::read('test.txt', 10000));
        $this->assertEquals("\n", File::read('test.txt', 1));
        $this->assertEquals('0123456789', File::read('test.txt', 10));
    }

    function testwrite()
    {
        $this->assertFalse(PEAR::isError($bytes = File::write('test.txt', '0123456789')));
        $this->assertEquals(10, $bytes);
    }

    function testreadChar()
    {
        $this->assertFalse(PEAR::isError(File::rewind('test.txt', FILE_MODE_READ)));
        $this->assertEquals('0', File::readChar('test.txt'));
        $this->assertEquals('1', File::readChar('test.txt'));
        $this->assertEquals('2', File::readChar('test.txt'));
        $this->assertEquals('3', File::readChar('test.txt'));
        $this->assertEquals('4', File::readChar('test.txt'));
        $this->assertEquals('5', File::readChar('test.txt'));
        $this->assertEquals('6', File::readChar('test.txt'));
        $this->assertEquals('7', File::readChar('test.txt'));
        $this->assertEquals('8', File::readChar('test.txt'));
        $this->assertEquals('9', File::readChar('test.txt'));
        $this->assertEquals('0', File::readChar('test.txt'));
    }

    function testwriteChar()
    {
        $this->assertEquals(1, File::writeChar('test.txt', 'a'));
        $this->assertEquals(1, File::writeChar('test.txt', 'b'));
        $this->assertEquals(1, File::writeChar('test.txt', 'c'));
        $this->assertEquals(1, File::writeChar('test.txt', 'd'));
        $this->assertEquals(1, File::writeChar('test.txt', 'e'));
        $this->assertEquals(1, File::writeChar('test.txt', 'f'));
        $this->assertEquals(1, File::writeChar('test.txt', 'g'));
        $this->assertEquals(1, File::writeChar('test.txt', 'h'));
        $this->assertEquals(1, File::writeChar('test.txt', 'i'));
        $this->assertEquals(1, File::writeChar('test.txt', 'j'));
    }

    function testreadLine()
    {
        $this->assertFalse(PEAR::isError(File::rewind('test.txt', FILE_MODE_READ)));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
        $this->assertEquals($this->getTestLine(), File::readLine('test.txt'));
    }

    function testwriteLine()
    {
        $line = $this->getTestLine();
        $length = strlen($line) + 1;
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
        $this->assertEquals($length, File::writeLine('test.txt', $line));
    }

    function testrewind()
    {
        $this->assertFalse(PEAR::isError(File::rewind('test.txt', FILE_MODE_WRITE)));
        $this->assertFalse(PEAR::isError(File::rewind('test.txt', FILE_MODE_READ)));
    }

    function testbuildPath()
    {
        $path = array(
            'some',
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            'weird'.DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR.'path'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,
        );
        $built = implode(DIRECTORY_SEPARATOR, array('some','weird','path','',''));
        $this->assertEquals($built, File::buildPath($path));
    }

    function testskipRoot()
    {
        if (OS_WINDOWS) {
            $this->assertEquals('WINDOWS', File::skipRoot('C:\\WINDOWS'));
            $this->assertEquals('WINDOWS', File::skipRoot('C:\\\\WINDOWS'));
            $this->assertEquals('WINDOWS', File::skipRoot('C:/WINDOWS'));
        } else {
            $this->assertEquals('usr/share/pear', File::skipRoot('/usr/share/pear'));
        }
    }

    function testgetTempDir()
    {
        $dir = File::getTempDir();
        $this->assertTrue(is_dir($dir), "is_dir($dir)");
    }

    function testgetTempFile()
    {
        $tmp = File::getTempFile();
        $this->assertTrue(file_exists($tmp));
    }

    function testisAbsolute()
    {
        $this->assertFalse(File::isAbsolute('abra/../cadabra'));
        $this->assertFalse(File::isAbsolute('data/dir'));
        if (OS_WINDOWS) {
            $this->assertTrue(File::isAbsolute('C:\\\\data'));
            $this->assertTrue(File::isAbsolute('d:/data'));
            $this->assertFalse(File::isAbsolute('\\'));
        } else {
            $this->assertTrue(File::isAbsolute('/'));
            $this->assertFalse(File::isAbsolute('\\'));
            $this->assertTrue(File::isAbsolute('~mike/bin'));
        }
    }

    function testrelativePath()
    {
        $this->assertEquals('tests/File', File::relativePath('/usr/share/pear/tests/File', '/usr/share/pear', '/'));
        $this->assertEquals('../etc', File::relativePath('/etc', '/usr', '/'));
        $this->assertEquals('D:\\Data', File::relativePath('D:\\Data', 'C:\\Data', '\\'));
        if (OS_WINDOWS) {
            $this->assertEquals('data\\dir', File::relativePath('/var/data/dir', '/var'));
        } else {
            $this->assertEquals('data/dir', File::relativePath('/var/data/dir', '/var'));
        }
        $this->assertEquals('../', File::relativePath('data', 'data/dir', '/'));
    }

    function testrealpath()
    {
        $drive = OS_WINDOWS ? substr(getcwd(),0, 2) :'';
        $this->assertEquals($drive . '/a/weird/path/is', File::realpath('/a\\weird//path\is/that/./../', '/'));
        $this->assertEquals($drive . '/a/weird/path/is/that', File::realpath('/a\\weird//path\is/that/./../that/.', '/'));
    }
}

$result = &PHPUnit::run(new PHPUnit_TestSuite('FileTest'));
echo $result->toString();

?>