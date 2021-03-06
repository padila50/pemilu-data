<?php

require_once('database.php');

$file = new SplFileObject("party_members.csv");
$file->setFlags(SplFileObject::READ_CSV);

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}

// Drop the target table
$dbh->query('TRUNCATE TABLE party_members');
$sth = $dbh->prepare('INSERT INTO party_members (
    id,
    party_id,
    name
) VALUES (
    :id,
    :party_id,
    :name
) ON DUPLICATE KEY UPDATE
    id = VALUES(id),
    party_id = VALUES(party_id),
    name = VALUES(name)
');

foreach ($file as $row) {
    if($row[0] != 'id') {
        $row = array_filter($row);
        if(!empty($row)){
            list(
                $id,
                $party_id,
                $name
            ) = $row;

            // Skip first row
            if ($id != 'id') {
                $sth->bindParam(':id', $id, PDO::PARAM_STR);
                $sth->bindParam(':party_id', $party_id, PDO::PARAM_STR);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                $sth->execute();

                echo $name . "\n";
            }
        }
    }
}
#$sth = $dbh->prepare("DELETE FROM party_members WHERE id not in ('".implode("','", $delete_id)."')");
#$sth->execute();
?>