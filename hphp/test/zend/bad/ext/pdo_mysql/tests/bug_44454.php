<?php
require dirname(__FILE__) . '/config.inc';
require dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');

function bug_44454($db) {

	try {

		$db->exec('DROP TABLE IF EXISTS test');
		$db->exec('CREATE TABLE test(a INT, b INT, UNIQUE KEY idx_ab (a, b))');
		$db->exec('INSERT INTO test(a, b) VALUES (1, 1)');

		$stmt = $db->query('SELECT a, b FROM test');
		printf("... SELECT has returned %d row...\n", $stmt->rowCount());
		while ($row = $stmt->fetch()) {
			try {
				printf("... INSERT should fail...\n");
				$db->exec('INSERT INTO test(a, b) VALUES (1, 1)');
			} catch (Exception $e) {
				printf("... STMT - %s\n", var_export($stmt->errorCode(), true));
				printf("... PDO  - %s\n", var_export($db->errorInfo(), true));
			}
		}

		$db->exec('DROP TABLE IF EXISTS test');
		$db->exec('CREATE TABLE test(a INT, b INT, UNIQUE KEY idx_ab (a, b))');
		$db->exec('INSERT INTO test(a, b) VALUES (1, 1)');

	} catch (Exception $e) {
		printf("... While error %s\n", $e->getMessage()); ;
	}

	$stmt = $db->query('SELECT a, b FROM test');
	printf("... SELECT has returned %d row...\n", $stmt->rowCount());
	foreach ($stmt as $row) {
		try {
			printf("... INSERT should fail...\n");
			$db->exec('INSERT INTO test(a, b) VALUES (1, 1)');
		} catch (Exception $e) {
			printf("... STMT - %s\n", var_export($stmt->errorCode(), true));
			printf("... PDO  - %s\n", var_export($db->errorInfo(), true));
		}
	}

}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

print "Native Prepared Statements\n";
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
bug_44454($db);

print "\nEmulated Prepared Statements\n";
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
bug_44454($db);

print "done!";
?>
<?php
require dirname(__FILE__) . '/mysql_pdo_test.inc';
$db = MySQLPDOTest::factory();
$db->exec('DROP TABLE IF EXISTS test');
?>