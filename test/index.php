<?

require_once( $_SERVER['DOCUMENT_ROOT'] . '/../base.php');

for ( $i=0; $i<2; $i++) {

    $user = DB_Peak_Users::fetch_by_id(1);
    var_dump($user->phones[0]->phone);

    $rand = rand(1000000000, 4294967295);
    var_dump($rand);
    $user->phones[0]->phone = $rand;

    $user->save();
}

Database_Connection::rollback();
$user = DB_Peak_Users::fetch_by_id(1);
var_dump($user->phones[0]->phone);



?>

<html>
    <body>

    <? include_once($_SERVER['R_DOCUMENT_ROOT'] . "/scripts/generate_db_model.php"); ?>

    </body>
</html>

<?

include_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
